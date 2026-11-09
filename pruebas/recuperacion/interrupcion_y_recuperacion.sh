#!/usr/bin/env bash
set -uo pipefail

RAIZ="$(cd "$(dirname "$0")/../.." && pwd)"
CICLO="${VALTRAZA_CICLO:-1}"
PG="${VALTRAZA_PG_BIN:-/c/Program Files/PostgreSQL/18/bin}"
HERD="${VALTRAZA_HERD:-$USERPROFILE/.config/herd/bin/herd.bat}"
URL="${VALTRAZA_URL:-https://valtraza.test}"
MODO="${1:-web}"
EVIDENCIA="$RAIZ/pruebas/evidencias/ciclo-$CICLO/recuperacion/CP0$([[ $MODO == web ]] && echo 78 || echo 79)_C${CICLO}_interrupcion_${MODO}.txt"
BASE="$(grep '^DB_DATABASE=' "$RAIZ/.env" | cut -d= -f2 | tr -d '\r')"
export PGPASSWORD="$(grep '^DB_PASSWORD=' "$RAIZ/.env" | cut -d= -f2 | tr -d '\r')"
USUARIO="$(grep '^DB_USERNAME=' "$RAIZ/.env" | cut -d= -f2 | tr -d '\r')"

mkdir -p "$(dirname "$EVIDENCIA")"
exec > >(tee "$EVIDENCIA") 2>&1

estado() { curl -s -k -o /dev/null -w '%{http_code}' --max-time 5 "$URL/$1" || true; }
cuerpo() { curl -s -k --max-time 5 "$URL/$1" || true; }
CODIGO="$("$PG/psql.exe" -h 127.0.0.1 -U "$USUARIO" -d "$BASE" -Atc "SELECT codigo_seguimiento FROM servicio ORDER BY id_servicio LIMIT 1" 2>/dev/null | tr -d '\r')"
consultar() {
    local tarro token
    tarro="$(mktemp)"
    token="$(curl -s -k -c "$tarro" --max-time 5 "$URL/" | sed -n 's/.*name="_token" value="\([^"]*\)".*/\1/p' | head -1)"
    CONSULTA_CUERPO="$(curl -s -k -b "$tarro" --max-time 10 -w '\n%{http_code}' --data-urlencode "_token=$token" --data-urlencode "codigo_seguimiento=$CODIGO" "$URL/consultar")"
    CONSULTA_ESTADO="$(tail -n1 <<<"$CONSULTA_CUERPO")"
    rm -f "$tarro"
}
conteos() { "$PG/psql.exe" -h 127.0.0.1 -U "$USUARIO" -d "$BASE" -Atc "SELECT (SELECT count(*) FROM cliente) || ' clientes, ' || (SELECT count(*) FROM servicio) || ' servicios, ' || (SELECT count(*) FROM diagnostico) || ' diagnosticos'" 2>&1 | tr -d '\r'; }

echo "Prueba de interrupción y recuperación ($MODO) · ciclo $CICLO · $(date '+%d-%m-%Y %H:%M:%S')"
echo "SHA probado: $(git -C "$RAIZ" rev-parse HEAD)"
echo "Antes: inicio HTTP $(estado '') · datos: $(conteos)"

if [[ $MODO == web ]]; then
    echo "1. Se detienen Nginx y PHP de Herd (simula caída del servidor web)"
    cmd //c "$HERD" stop >/dev/null 2>&1
    sleep 3
    echo "   Durante la caída: inicio HTTP $(estado '') (000 = sin respuesta)"
    echo "2. Se inician de nuevo los servicios, sin reinstalar ni recrear tablas"
    INICIO=$(date +%s)
    cmd //c "$HERD" start >/dev/null 2>&1
    for i in $(seq 1 30); do [[ "$(estado '')" == 200 ]] && break; sleep 1; done
    echo "   Disponible otra vez tras $(( $(date +%s) - INICIO )) s · inicio HTTP $(estado '') · ingresar HTTP $(estado ingresar)"
else
    echo "1. Se espera la detención del servicio postgresql-x64-18 (ventana elevada aprobada por el usuario)"
    for i in $(seq 1 60); do [[ "$(sc query postgresql-x64-18 | grep -c STOPPED)" == 1 ]] && break; sleep 1; done
    echo "   Servicio: $(sc query postgresql-x64-18 | grep ESTADO\\\|STATE | tr -s ' ')"
    echo "   Durante la caída: inicio pública HTTP $(estado '') (no consulta la base)"
    consultar
    echo "   Durante la caída: consulta pública que sí usa la base HTTP $CONSULTA_ESTADO"
    if grep -qiE 'SQLSTATE|pgsql|PDOException|valtraza_pruebas|DB_PASSWORD|Stack trace|vendor/laravel' <<<"$CONSULTA_CUERPO"; then echo "   La página de error expone detalles internos"; else echo "   La página de error no expone SQL, credenciales ni trazas"; fi
    echo "   Título mostrado: $(sed -n 's:.*<h1[^>]*>\([^<]*\)</h1>.*:\1:p' <<<"$CONSULTA_CUERPO" | head -1)"
    echo "2. Se espera el reinicio del servicio (sin reinstalar ni recrear tablas)"
    INICIO=$(date +%s)
    for i in $(seq 1 120); do consultar; [[ "$CONSULTA_ESTADO" == 200 ]] && break; sleep 1; done
    echo "   Disponible otra vez tras $(( $(date +%s) - INICIO )) s · consulta pública HTTP $CONSULTA_ESTADO"
fi

echo "Después: datos: $(conteos)"
