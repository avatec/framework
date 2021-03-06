<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitaa04088e38db7e6fa567c0581d8c9986
{
    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'Core\\Api' => __DIR__ . '/../..' . '/src/core/api.php',
        'Core\\Assets' => __DIR__ . '/../..' . '/src/core/assets.php',
        'Core\\Backend\\Messages' => __DIR__ . '/../..' . '/src/core/backend/messages.php',
        'Core\\Backend\\Meta' => __DIR__ . '/../..' . '/src/core/backend/meta.php',
        'Core\\Backend\\Model' => __DIR__ . '/../..' . '/src/core/backend/model.php',
        'Core\\Backend\\Navigation' => __DIR__ . '/../..' . '/src/core/backend/navigation.php',
        'Core\\Backend\\Panel' => __DIR__ . '/../..' . '/src/core/backend/panel.php',
        'Core\\Cache' => __DIR__ . '/../..' . '/src/core/cache.php',
        'Core\\Cli\\Messages' => __DIR__ . '/../..' . '/src/core/cli/messages.php',
        'Core\\Common' => __DIR__ . '/../..' . '/src/core/common.php',
        'Core\\Components' => __DIR__ . '/../..' . '/src/core/components.php',
        'Core\\Database' => __DIR__ . '/../..' . '/src/core/database.php',
        'Core\\Database\\Database' => __DIR__ . '/../..' . '/src/core/database/database.php',
        'Core\\Database\\Drivers\\DriverSQLite' => __DIR__ . '/../..' . '/src/core/database/drivers/sqlite.php',
        'Core\\Database\\Drivers\\MySQLi' => __DIR__ . '/../..' . '/src/core/database/drivers/mysqli.php',
        'Core\\Database\\Drivers\\PDO' => __DIR__ . '/../..' . '/src/core/database/drivers/pdo.php',
        'Core\\Database\\Drivers\\PgSQL' => __DIR__ . '/../..' . '/src/core/database/drivers/pgsql.php',
        'Core\\Database\\Interfaces\\AdapterInterface' => __DIR__ . '/../..' . '/src/core/database/interfaces/AdapterInterface.php',
        'Core\\Date' => __DIR__ . '/../..' . '/src/core/date.php',
        'Core\\Db' => __DIR__ . '/../..' . '/src/core/db.php',
        'Core\\Error' => __DIR__ . '/../..' . '/src/core/error.php',
        'Core\\FacebookClient' => __DIR__ . '/../..' . '/src/core/facebook.php',
        'Core\\Fakturownia' => __DIR__ . '/../..' . '/src/core/fakturownia.php',
        'Core\\Files' => __DIR__ . '/../..' . '/src/core/files.php',
        'Core\\FilesTokens' => __DIR__ . '/../..' . '/src/core/FilesTokens.php',
        'Core\\Form' => __DIR__ . '/../..' . '/src/core/form.php',
        'Core\\Frontend\\Breadcrumb' => __DIR__ . '/../..' . '/src/core/frontend/breadcrumb.php',
        'Core\\Frontend\\Language' => __DIR__ . '/../..' . '/src/core/frontend/language.php',
        'Core\\Frontend\\Messages' => __DIR__ . '/../..' . '/src/core/frontend/messages.php',
        'Core\\Frontend\\Meta' => __DIR__ . '/../..' . '/src/core/frontend/meta.php',
        'Core\\Frontend\\Model' => __DIR__ . '/../..' . '/src/core/frontend/model.php',
        'Core\\Frontend\\Opengraph' => __DIR__ . '/../..' . '/src/core/frontend/opengraph.php',
        'Core\\Image' => __DIR__ . '/../..' . '/src/core/image.php',
        'Core\\InstagramBasicAPI' => __DIR__ . '/../..' . '/src/core/instagrambasicapi.php',
        'Core\\InstagramPlugin' => __DIR__ . '/../..' . '/src/core/instagramplugin.php',
        'Core\\Kernel' => __DIR__ . '/../..' . '/src/core/kernel.php',
        'Core\\Language' => __DIR__ . '/../..' . '/src/core/language.php',
        'Core\\LanguageBackend' => __DIR__ . '/../..' . '/src/core/languageBackend.php',
        'Core\\Language\\LanguageFolderNotFoundException' => __DIR__ . '/../..' . '/src/core/language/LanguageFoldernNotFoundException.php',
        'Core\\Language\\LanguageTransactionNotFoundException' => __DIR__ . '/../..' . '/src/core/language/LanguageTransactionNotFoundException.php',
        'Core\\Lingua' => __DIR__ . '/../..' . '/src/core/lingua.php',
        'Core\\Logs' => __DIR__ . '/../..' . '/src/core/logs.php',
        'Core\\Mail' => __DIR__ . '/../..' . '/src/core/mail.php',
        'Core\\Paginate' => __DIR__ . '/../..' . '/src/core/paginate.php',
        'Core\\Postcodes' => __DIR__ . '/../..' . '/src/core/postcodes.php',
        'Core\\Request' => __DIR__ . '/../..' . '/src/core/request.php',
        'Core\\Route' => __DIR__ . '/../..' . '/src/core/route.php',
        'Core\\SMSGateway' => __DIR__ . '/../..' . '/src/core/smsgateway.php',
        'Core\\Views' => __DIR__ . '/../..' . '/src/core/views.php',
        'Core\\Wysiwyg' => __DIR__ . '/../..' . '/src/core/wysiwyg.php',
        'Exception\\DatabaseConnectErrorException' => __DIR__ . '/../..' . '/src/core/database/exceptions/DatabaseConnectErrorException.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInitaa04088e38db7e6fa567c0581d8c9986::$classMap;

        }, null, ClassLoader::class);
    }
}
