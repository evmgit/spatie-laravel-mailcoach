const colors = require('tailwindcss/colors');

module.exports = {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './vendor/wire-elements/spotlight/resources/views/spotlight.blade.php',
        '../mailcoach-ui/resources/**/*.blade.php',
        '../mailcoach-packages/packages/*/resources/**/*.blade.php',
      ],
    theme: {
        colors: {
            transparent: 'transparent',
            current: 'currentColor',
            black: colors.black,
            white: colors.white,
            gray: colors.slate,
            blue: colors.blue,
            indigo: colors.indigo,
            teal: colors.teal,
            red: colors.rose,
            green: colors.emerald,
            yellow: colors.amber,
            orange: colors.orange,
            purple: colors.purple,
        },
        extend: {
            boxShadow: {
                focus: '0 2px 2px #e5e3e1' ,
            },
            fontFamily: {
                awesome: '"Font Awesome 5 Pro"',
            },
            gridTemplateColumns: {
                auto: 'auto',
                'auto-1fr': 'auto 1fr',
                '1fr-auto': '1fr auto',
            },
            inset: {
                full: '100%',
            },
            height: {
                '2px': '2px',
            },
            minHeight: {
                4: '1rem',
                6: '1.5rem',
                8: '2rem',
                9: '2.25rem',
                10: '2.5rem',
            },
            minWidth: {
                4: '1rem',
                6: '1.5rem',
                8: '2rem',
                10: '2.5rem',
                32: '8rem',
            },
            maxWidth: {
                layout: '100rem',
            },
            backgroundSize: {
                'size-200': '200% 200%',
            },
            backgroundPosition: {
                'pos-0': '0% 0%',
                'pos-100': '100% 100%',
            },
            keyframes: {
                scale: {
                    '0%, 100%': { transform: 'scale(1)' },
                    '25%': { transform: 'scale(1.05)' },
                    '50%': { transform: 'scale(1)' },
                    '75%': { transform: 'scale(1.05)' },
                }
            },
            animation: {
                scale: 'scale 300ms ease-in-out',
            }
        },
    },
    corePlugins: {
        ringWidth: false,
    }
};
