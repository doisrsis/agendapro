@echo off
echo Instalando biblioteca piggly/php-pix...
echo.

cd /d c:\xampp\htdocs\agendapro

REM Tentar encontrar composer
where composer >nul 2>&1
if %errorlevel% equ 0 (
    echo Usando composer global...
    composer install
) else (
    REM Tentar usar composer.phar local
    if exist composer.phar (
        echo Usando composer.phar local...
        c:\xampp\php\php.exe composer.phar install
    ) else (
        echo Baixando composer...
        c:\xampp\php\php.exe -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
        c:\xampp\php\php.exe composer-setup.php
        c:\xampp\php\php.exe -r "unlink('composer-setup.php');"
        c:\xampp\php\php.exe composer.phar install
    )
)

echo.
echo Instalacao concluida!
pause
