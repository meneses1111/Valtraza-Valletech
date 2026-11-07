CREATE TYPE rol_usuario     AS ENUM ('administrador', 'tecnico');
CREATE TYPE estado_servicio AS ENUM ('pendiente', 'en_curso', 'completado');
CREATE TYPE tipo_trabajo    AS ENUM ('mantenimiento', 'reparacion');
CREATE TABLE cliente (
    id_cliente            INTEGER       GENERATED ALWAYS AS IDENTITY,
    nombre                VARCHAR(100)  NOT NULL,
    numero_identificacion VARCHAR(20)   NOT NULL,
    correo                VARCHAR(100)  NULL,
    CONSTRAINT pk_cliente PRIMARY KEY (id_cliente),
    CONSTRAINT uq_cliente_identificacion UNIQUE (numero_identificacion),
    CONSTRAINT ck_cliente_textos CHECK (btrim(nombre) <> '' AND btrim(numero_identificacion) <> '')
);
CREATE TABLE tecnico (
    id_tecnico            INTEGER       GENERATED ALWAYS AS IDENTITY,
    nombre                VARCHAR(100)  NOT NULL,
    numero_identificacion VARCHAR(20)   NOT NULL,
    correo                VARCHAR(100)  NULL,
    CONSTRAINT pk_tecnico PRIMARY KEY (id_tecnico),
    CONSTRAINT uq_tecnico_identificacion UNIQUE (numero_identificacion),
    CONSTRAINT ck_tecnico_textos CHECK (btrim(nombre) <> '' AND btrim(numero_identificacion) <> '')
);
CREATE TABLE usuario (
    id_usuario        INTEGER       GENERATED ALWAYS AS IDENTITY,
    nombre_usuario    VARCHAR(50)   NOT NULL,
    contrasena_hash   VARCHAR(255)  NULL,
    sal               VARCHAR(64)   NULL,
    rol               rol_usuario   NOT NULL,
    codigo_activacion VARCHAR(64)   NOT NULL,
    activado          BOOLEAN       NOT NULL DEFAULT FALSE,
    id_tecnico        INTEGER       NULL,
    CONSTRAINT pk_usuario PRIMARY KEY (id_usuario),
    CONSTRAINT uq_usuario_nombre UNIQUE (nombre_usuario),
    CONSTRAINT uq_usuario_activacion UNIQUE (codigo_activacion),
    CONSTRAINT uq_usuario_tecnico UNIQUE (id_tecnico),
    CONSTRAINT fk_usuario_tecnico FOREIGN KEY (id_tecnico)
        REFERENCES tecnico (id_tecnico) ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT ck_usuario_rol_vinculo CHECK (
           (rol = 'administrador' AND id_tecnico IS NULL)
        OR (rol = 'tecnico'       AND id_tecnico IS NOT NULL)),
    CONSTRAINT ck_usuario_activado CHECK (NOT activado OR contrasena_hash IS NOT NULL),
    CONSTRAINT ck_usuario_textos CHECK (btrim(nombre_usuario) <> '' AND btrim(codigo_activacion) <> '')
);
CREATE TABLE equipo (
    id_equipo            INTEGER       GENERATED ALWAYS AS IDENTITY,
    codigo_equipo        VARCHAR(20)   NOT NULL,
    id_cliente           INTEGER       NOT NULL,
    fecha_registro       DATE          NOT NULL,
    modelo               VARCHAR(100)  NULL,
    otros_datos_tecnicos TEXT          NULL,
    CONSTRAINT pk_equipo PRIMARY KEY (id_equipo),
    CONSTRAINT uq_equipo_codigo UNIQUE (codigo_equipo),
    CONSTRAINT fk_equipo_cliente FOREIGN KEY (id_cliente)
        REFERENCES cliente (id_cliente) ON UPDATE RESTRICT ON DELETE RESTRICT
);
CREATE TABLE servicio (
    id_servicio          INTEGER          GENERATED ALWAYS AS IDENTITY,
    codigo_servicio      VARCHAR(20)      NOT NULL,
    codigo_seguimiento   VARCHAR(32)      NOT NULL,
    id_equipo            INTEGER          NOT NULL,
    id_tecnico           INTEGER          NULL,
    fecha                DATE             NOT NULL,
    nombre               VARCHAR(100)     NOT NULL,
    descripcion_problema TEXT             NOT NULL,
    estado               estado_servicio  NOT NULL DEFAULT 'pendiente',
    CONSTRAINT pk_servicio PRIMARY KEY (id_servicio),
    CONSTRAINT uq_servicio_codigo UNIQUE (codigo_servicio),
    CONSTRAINT uq_servicio_seguimiento UNIQUE (codigo_seguimiento),
    CONSTRAINT fk_servicio_equipo FOREIGN KEY (id_equipo)
        REFERENCES equipo (id_equipo) ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT fk_servicio_tecnico FOREIGN KEY (id_tecnico)
        REFERENCES tecnico (id_tecnico) ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT ck_servicio_estado_tecnico CHECK (estado = 'pendiente' OR id_tecnico IS NOT NULL),
    CONSTRAINT ck_servicio_textos CHECK (btrim(nombre) <> '' AND btrim(descripcion_problema) <> '')
);
CREATE TABLE diagnostico (
    id_diagnostico       INTEGER    GENERATED ALWAYS AS IDENTITY,
    id_servicio          INTEGER    NOT NULL,
    resultado            TEXT       NOT NULL,
    fallas_identificadas TEXT       NULL,
    pruebas_realizadas   TEXT       NULL,
    observaciones        TEXT       NULL,
    fecha_registro       TIMESTAMP  NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT pk_diagnostico PRIMARY KEY (id_diagnostico),
    CONSTRAINT fk_diagnostico_servicio FOREIGN KEY (id_servicio)
        REFERENCES servicio (id_servicio) ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT ck_diagnostico_textos CHECK (btrim(resultado) <> '')
);
CREATE TABLE trabajo (
    id_trabajo     INTEGER       GENERATED ALWAYS AS IDENTITY,
    id_servicio    INTEGER       NOT NULL,
    tipo           tipo_trabajo  NOT NULL,
    descripcion    TEXT          NOT NULL,
    observaciones  TEXT          NULL,
    fecha_registro TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT pk_trabajo PRIMARY KEY (id_trabajo),
    CONSTRAINT fk_trabajo_servicio FOREIGN KEY (id_servicio)
        REFERENCES servicio (id_servicio) ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT ck_trabajo_textos CHECK (btrim(descripcion) <> '')
);
CREATE TABLE repuesto (
    id_repuesto    INTEGER       GENERATED ALWAYS AS IDENTITY,
    id_servicio    INTEGER       NOT NULL,
    nombre         VARCHAR(100)  NOT NULL,
    cantidad       INTEGER       NOT NULL,
    observacion    VARCHAR(255)  NULL,
    fecha_registro TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT pk_repuesto PRIMARY KEY (id_repuesto),
    CONSTRAINT fk_repuesto_servicio FOREIGN KEY (id_servicio)
        REFERENCES servicio (id_servicio) ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT ck_repuesto_cantidad CHECK (cantidad > 0),
    CONSTRAINT ck_repuesto_textos CHECK (btrim(nombre) <> '')
);
