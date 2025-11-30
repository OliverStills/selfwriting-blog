@echo off
setlocal
set PATH=%USERPROFILE%\php;%PATH%

echo ========================================
echo  GENERATING POSTS FOR ALL FOUR PILLARS
echo  The Fifth State Framework
echo ========================================
echo.

echo [1/4] Generating STILL post...
php generate-single-pillar.php STILL
if %ERRORLEVEL% NEQ 0 (
    echo ERROR generating STILL post!
    pause
    exit /b 1
)
echo.

echo [2/4] Generating GRIT post...
php generate-single-pillar.php GRIT
if %ERRORLEVEL% NEQ 0 (
    echo ERROR generating GRIT post!
    pause
    exit /b 1
)
echo.

echo [3/4] Generating REFLECTION post...
php generate-single-pillar.php REFLECTION
if %ERRORLEVEL% NEQ 0 (
    echo ERROR generating REFLECTION post!
    pause
    exit /b 1
)
echo.

echo [4/4] Generating ASCEND post...
php generate-single-pillar.php ASCEND
if %ERRORLEVEL% NEQ 0 (
    echo ERROR generating ASCEND post!
    pause
    exit /b 1
)
echo.

echo ========================================
echo  ALL FOUR PILLARS COMPLETE!
echo ========================================
echo.
echo Visit http://localhost:8000 to see your posts!
echo.
pause




