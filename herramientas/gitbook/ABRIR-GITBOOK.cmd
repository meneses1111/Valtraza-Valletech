@echo off
title GitBook de Valtraza - Vista previa
cd /d "%~dp0"
where node >nul 2>&1
if errorlevel 1 (
    echo Esta vista previa necesita Node.js 18 o posterior.
    echo Tambien puedes servir esta carpeta con otro servidor HTTP local.
    pause
    exit /b 1
)
node abrir-vista-previa.cjs
if errorlevel 1 pause
