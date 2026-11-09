#!/usr/bin/env bash
set -euo pipefail

RAIZ="$(cd "$(dirname "$0")/../.." && pwd)"
CICLO="${VALTRAZA_CICLO:-1}"
PG="${VALTRAZA_PG_BIN:-/c/Program Files/PostgreSQL/18/bin}"
PHP="${VALTRAZA_PHP:-$USERPROFILE/.config/herd/bin/php83/php.exe}"
PRIVADO="${VALTRAZA_PRIVADO:-$RAIZ/../privado}"
EVIDENCIA="$RAIZ/pruebas/evidencias/ciclo-$CICLO/recuperacion/CP066_C${CICLO}_copia_y_restauracion.txt"
ORIGEN="$(grep '^DB_DATABASE=' "$RAIZ/.env" | cut -d= -f2 | tr -d '\r')"
USUARIO="$(grep '^DB_USERNAME=' "$RAIZ/.env" | cut -d= -f2 | tr -d '\r')"
export PGPASSWORD="$(grep '^DB_PASSWORD=' "$RAIZ/.env" | cut -d= -f2 | tr -d '\r')"
DESTINO="valtraza_restauracion"
RESPALDO="$PRIVADO/respaldos/${ORIGEN}_$(date +%Y%m%d_%H%M%S).backup"
CONEXION=(-h 127.0.0.1 -p 5432 -U "$USUARIO")

mkdir -p "$(dirname "$EVIDENCIA")" "$PRIVADO/respaldos"
exec > >(tee "$EVIDENCIA") 2>&1

conteos() {
    "$PG/psql.exe" "${CONEXION[@]}" -d "$1" -Atc "SELECT string_agg(t || '=' || n, ' ' ORDER BY t) FROM (
        SELECT 'cliente' t, count(*) n FROM cliente UNION ALL SELECT 'tecnico', count(*) FROM tecnico UNION ALL
        SELECT 'usuario', count(*) FROM usuario UNION ALL SELECT 'equipo', count(*) FROM equipo UNION ALL
        SELECT 'servicio', count(*) FROM servicio UNION ALL SELECT 'diagnostico', count(*) FROM diagnostico UNION ALL
        SELECT 'trabajo', count(*) FROM trabajo UNION ALL SELECT 'repuesto', count(*) FROM repuesto) c" | tr -d '\r'
}

huella() {
    "$PG/psql.exe" "${CONEXION[@]}" -d "$1" -Atc "SELECT md5(string_agg(codigo_servicio || estado || coalesce(id_tecnico::text, '-') || nombre, '|' ORDER BY id_servicio)) FROM servicio" | tr -d '\r'
}

echo "Prueba de copia y restauración · ciclo $CICLO · $(date '+%d-%m-%Y %H:%M:%S')"
echo "SHA probado: $(git -C "$RAIZ" rev-parse HEAD)"
echo "PostgreSQL: $("$PG/pg_dump.exe" --version)"
echo "Base de origen: $ORIGEN (datos ficticios)"
echo
echo "1. Conteos y huella antes de la copia"
ANTES="$(conteos "$ORIGEN")"; HUELLA_ANTES="$(huella "$ORIGEN")"
echo "   $ANTES"; echo "   huella servicios: $HUELLA_ANTES"

echo "2. Copia con pg_dump en formato personalizado (ubicación privada fuera del repositorio)"
"$PG/pg_dump.exe" "${CONEXION[@]}" -Fc -f "$RESPALDO" "$ORIGEN"
echo "   código de salida: $? · tamaño: $(stat -c %s "$RESPALDO") bytes"

echo "3. Base de restauración vacía e independiente"
"$PG/dropdb.exe" "${CONEXION[@]}" --if-exists "$DESTINO"
"$PG/createdb.exe" "${CONEXION[@]}" -E UTF8 -T template0 "$DESTINO"
"$PG/pg_restore.exe" "${CONEXION[@]}" --no-owner --exit-on-error -d "$DESTINO" "$RESPALDO"
echo "   pg_restore código de salida: $?"

echo "4. Comparación de conteos y huella"
DESPUES="$(conteos "$DESTINO")"; HUELLA_DESPUES="$(huella "$DESTINO")"
echo "   $DESPUES"; echo "   huella servicios: $HUELLA_DESPUES"
if [[ "$ANTES" == "$DESPUES" && "$HUELLA_ANTES" == "$HUELLA_DESPUES" ]]; then echo "   RESULTADO: coinciden"; else echo "   RESULTADO: NO coinciden"; exit 1; fi

echo "5. La aplicación funciona apuntada a la base restaurada (copia en http://127.0.0.1:8099)"
CODIGO="$("$PG/psql.exe" "${CONEXION[@]}" -d "$DESTINO" -Atc "SELECT codigo_seguimiento FROM servicio WHERE estado = 'completado' ORDER BY id_servicio LIMIT 1" | tr -d '\r')"
NOMBRE="$("$PG/psql.exe" "${CONEXION[@]}" -d "$DESTINO" -Atc "SELECT nombre FROM servicio WHERE codigo_seguimiento = '$CODIGO'" | tr -d '\r')"
( cd "$RAIZ" && DB_DATABASE="$DESTINO" SESSION_SECURE_COOKIE=false APP_URL=http://127.0.0.1:8099 "$PHP" -S 127.0.0.1:8099 -t public public/index.php >/dev/null 2>&1 ) &
SERVIDOR=$!
sleep 3
TARRO="$(mktemp)"
TOKEN="$(curl -s -c "$TARRO" http://127.0.0.1:8099/ | sed -n 's/.*name="_token" value="\([^"]*\)".*/\1/p' | head -1)"
RESPUESTA="$(curl -s -b "$TARRO" -c "$TARRO" -o /dev/null -w '%{http_code}' --data-urlencode "_token=$TOKEN" --data-urlencode "codigo_seguimiento=$CODIGO" http://127.0.0.1:8099/consultar)"
CUERPO="$(curl -s -b "$TARRO" --data-urlencode "_token=$TOKEN" --data-urlencode "codigo_seguimiento=$CODIGO" http://127.0.0.1:8099/consultar)"
kill "$SERVIDOR" 2>/dev/null || true
rm -f "$TARRO"
echo "   consulta pública en la copia: HTTP $RESPUESTA"
if grep -q "$NOMBRE" <<<"$CUERPO" && grep -q "Completado" <<<"$CUERPO"; then echo "   RESULTADO: la copia muestra el servicio restaurado ($NOMBRE, Completado)"; else echo "   RESULTADO: la copia NO muestra el servicio"; exit 1; fi

echo "6. Limpieza: se elimina solo la base de restauración de prueba; la base de origen no se modificó"
"$PG/dropdb.exe" "${CONEXION[@]}" "$DESTINO"
echo "   conteos de origen al final: $(conteos "$ORIGEN")"
