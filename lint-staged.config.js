module.exports = {
    'resources/{css,js}/**/*.{css,js}': ['prettier --write', 'git add'],
    'resources/**/*.{css,js}': () => ['npm run build', 'git add resources/dist'],
    //'**/*.php': ['php ./vendor/bin/php-cs-fixer fix --config .php_cs', 'git add'],
};
