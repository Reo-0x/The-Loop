<?php
session_start();
require_once('config/db_connect.php');
require_once('includes/layout.php');

// Check if user is already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: pages/dashboard.php");
    exit;
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];
    
    $sql = "SELECT * FROM users WHERE username='$username' LIMIT 1";
    $result = $conn->query($sql);
    if ($result && $result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: pages/dashboard.php");
            exit;
        } else {
            $login_message = "Invalid username or password.";
        }
    } else {
        $login_message = "User not found.";
    }
}

// Handle signup
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'signup') {
    $username = $conn->real_escape_string($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check_sql = "SELECT * FROM users WHERE username='$username' LIMIT 1";
    $check_result = $conn->query($check_sql);
    
    if ($check_result && $check_result->num_rows > 0) {
        $signup_message = "Username already exists. Please choose another.";
    } else {
        $sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
        
        if ($conn->query($sql) === TRUE) {
            $signup_message = "Registration successful. Please log in.";
            $show_login = true;
        } else {
            $signup_message = "Error: " . $conn->error;
        }
    }
}

$show_login = isset($_GET['form']) && $_GET['form'] === 'signup' ? false : true;
if (isset($signup_message) && $signup_message === "Registration successful. Please log in.") {
    $show_login = true;
}

renderHeader('Welcome');
?>

<div class="min-h-screen flex flex-col items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-cyber-dark/70 p-8 rounded-lg cyber-border backdrop-blur-sm">
        <!-- Logo/Brand -->
        <div class="text-center">
            <h1 class="text-4xl font-bold text-cyber-pink mb-2 animate-glow">The Loop</h1>
            <p class="text-cyber-neon opacity-80">Track your weekly objectives with style</p>
        </div>

        <!-- Form Toggle -->
        <div class="flex justify-center space-x-4 border-b border-cyber-pink/30">
            <button id="login-toggle" 
                    class="pb-2 px-4 text-sm font-medium <?php echo $show_login ? 'text-cyber-pink border-b-2 border-cyber-pink shadow-neon-pink' : 'text-gray-400'; ?> 
                           hover:text-cyber-pink transition-colors">
                Login
            </button>
            <button id="signup-toggle" 
                    class="pb-2 px-4 text-sm font-medium <?php echo !$show_login ? 'text-cyber-pink border-b-2 border-cyber-pink shadow-neon-pink' : 'text-gray-400'; ?> 
                           hover:text-cyber-pink transition-colors">
                Sign Up
            </button>
        </div>

        <!-- Login Form -->
        <div id="login-form" class="<?php echo $show_login ? 'block' : 'hidden'; ?> space-y-6">
            <?php if (isset($login_message)): ?>
                <div class="p-3 rounded-lg bg-cyber-dark/50 text-cyber-neon text-sm cyber-border">
                    <?php echo $login_message; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="index.php" class="space-y-4">
                <input type="hidden" name="action" value="login">
                
                <div>
                    <label for="login-username" class="block text-sm font-medium text-cyber-neon mb-1">Username</label>
                    <input type="text" id="login-username" name="username" required
                           class="cyber-input w-full px-3 py-2 rounded-md">
                </div>

                <div>
                    <label for="login-password" class="block text-sm font-medium text-cyber-neon mb-1">Password</label>
                    <input type="password" id="login-password" name="password" required
                           class="cyber-input w-full px-3 py-2 rounded-md">
                </div>

                <button type="submit" 
                        class="cyber-button w-full py-2 px-4 rounded-md">
                    <i class="fas fa-sign-in-alt mr-2"></i> Login
                </button>
            </form>
        </div>

        <!-- Signup Form -->
        <div id="signup-form" class="<?php echo !$show_login ? 'block' : 'hidden'; ?> space-y-6">
            <?php if (isset($signup_message)): ?>
                <div class="p-3 rounded-lg bg-cyber-dark/50 <?php echo $signup_message === 'Registration successful. Please log in.' ? 'text-cyber-neon' : 'text-cyber-pink'; ?> text-sm cyber-border">
                    <?php echo $signup_message; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="index.php" class="space-y-4">
                <input type="hidden" name="action" value="signup">
                
                <div>
                    <label for="signup-username" class="block text-sm font-medium text-cyber-neon mb-1">Username</label>
                    <input type="text" id="signup-username" name="username" required
                           class="cyber-input w-full px-3 py-2 rounded-md">
                </div>

                <div>
                    <label for="signup-password" class="block text-sm font-medium text-cyber-neon mb-1">Password</label>
                    <input type="password" id="signup-password" name="password" required
                           class="cyber-input w-full px-3 py-2 rounded-md">
                </div>

                <button type="submit" 
                        class="cyber-button w-full py-2 px-4 rounded-md">
                    <i class="fas fa-user-plus mr-2"></i> Sign Up
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('login-toggle').addEventListener('click', function() {
    document.getElementById('login-form').classList.remove('hidden');
    document.getElementById('signup-form').classList.add('hidden');
    this.classList.add('text-cyber-pink', 'border-b-2', 'border-cyber-pink', 'shadow-neon-pink');
    document.getElementById('signup-toggle').classList.remove('text-cyber-pink', 'border-b-2', 'border-cyber-pink', 'shadow-neon-pink');
});

document.getElementById('signup-toggle').addEventListener('click', function() {
    document.getElementById('signup-form').classList.remove('hidden');
    document.getElementById('login-form').classList.add('hidden');
    this.classList.add('text-cyber-pink', 'border-b-2', 'border-cyber-pink', 'shadow-neon-pink');
    document.getElementById('login-toggle').classList.remove('text-cyber-pink', 'border-b-2', 'border-cyber-pink', 'shadow-neon-pink');
});
</script>

<?php renderFooter(); ?>
