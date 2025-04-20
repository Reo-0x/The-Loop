<?php
function renderHeader($title) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?> - Hebdomadaires</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#FF0080',
                        secondary: '#7928CA',
                        accent: '#F81CE5',
                        cyber: {
                            neon: '#0FF',
                            pink: '#FF0080',
                            purple: '#7928CA',
                            blue: '#2563EB',
                            dark: '#0A0A0A',
                        }
                    },
                    boxShadow: {
                        'neon': '0 0 3px theme("colors.cyber.neon"), 0 0 8px theme("colors.cyber.neon")',
                        'neon-pink': '0 0 3px theme("colors.cyber.pink"), 0 0 8px theme("colors.cyber.pink")',
                    },
                    animation: {
                        'glow': 'glow 2s ease-in-out infinite alternate',
                    },
                    keyframes: {
                        glow: {
                            'from': { 'text-shadow': '0 0 3px #fff, 0 0 5px #fff, 0 0 7px theme("colors.cyber.pink"), 0 0 10px theme("colors.cyber.pink")' },
                            'to': { 'text-shadow': '0 0 5px #fff, 0 0 10px #fff, 0 0 15px theme("colors.cyber.pink"), 0 0 20px theme("colors.cyber.pink")' },
                        }
                    }
                }
            }
        }
    </script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700&display=swap');
        
        .font-cyber {
            font-family: 'Orbitron', sans-serif;
        }
        
        .cyber-bg {
            background: linear-gradient(135deg, #121212 0%, #1E1E1E 100%);
        }
        
        .cyber-border {
            border: 1px solid rgba(255, 0, 128, 0.5);
            box-shadow: 0 0 5px rgba(255, 0, 128, 0.3);
        }
        
        .flash-message {
            position: fixed;
            top: 1rem;
            left: 50%;
            transform: translate(-50%, -150%);
            z-index: 50;
            transition: transform 0.5s ease, opacity 0.5s ease;
            opacity: 0;
            background: rgba(20, 20, 20, 0.85);
            border: 1px solid rgba(0, 255, 255, 0.5);
            box-shadow: 0 0 5px rgba(0, 255, 255, 0.3);
        }

        .flash-message.show {
            transform: translate(-50%, 0);
            opacity: 1;
        }
        
        .cyber-input {
            background: rgba(20, 20, 20, 0.7);
            border: 1px solid rgba(255, 0, 128, 0.5);
            color: white;
            transition: all 0.3s ease;
        }
        
        .cyber-input:focus {
            box-shadow: 0 0 8px rgba(255, 0, 128, 0.4);
            outline: none;
        }
        
        .cyber-button {
            background: linear-gradient(45deg, rgba(255, 0, 128, 0.9), rgba(121, 40, 202, 0.9));
            color: white;
            font-family: 'Orbitron', sans-serif;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }
        
        .cyber-button:hover {
            box-shadow: 0 0 8px rgba(255, 0, 128, 0.5);
            transform: translateY(-1px);
            filter: brightness(1.1);
        }
    </style>
</head>
<body class="cyber-bg text-white min-h-screen font-cyber">
    <?php if (isset($_SESSION['message'])): ?>
        <div id="flashMessage" class="flash-message px-4 py-2 rounded-lg">
            <?php echo $_SESSION['message']['text']; ?>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const flashMessage = document.getElementById('flashMessage');
                if (flashMessage) {
                    requestAnimationFrame(() => {
                        flashMessage.classList.add('show');
                    });
                    setTimeout(() => {
                        flashMessage.classList.remove('show');
                        setTimeout(() => {
                            flashMessage.remove();
                        }, 500);
                    }, 3000);
                }
            });
        </script>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
<?php
}

function renderFooter() {
?>
    <footer class="mt-8 py-4 text-center text-gray-600 text-sm">
        <p>&copy; <?php echo date('Y'); ?> Hebdomadaires. All rights reserved.</p>
    </footer>
</body>
</html>
<?php
}
?>
