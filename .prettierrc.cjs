module.exports = {
    printWidth: 120,
    singleQuote: true,
    trailingComma: 'es5',
    plugins: [ require( 'prettier-plugin-blade' ) ],
    overrides: [
        {
            files: '*.blade.php',
            options: {
                parser: 'blade',
            },
        },
    ],
};
