<?php
session_start();
require_once('../config/db_connect.php');
require_once('../includes/layout.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

// Process new objective submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_objective'])) {
    $objective = $conn->real_escape_string($_POST['objective']);
    $day_of_week = (int)$_POST['day_of_week'];
    $user_id = $_SESSION['user_id'];
    
    if ($day_of_week >= 1 && $day_of_week <= 7) {
        $sql = "INSERT INTO objectives (user_id, objective, day_of_week, status) VALUES ($user_id, '$objective', $day_of_week, 0)";
        if ($conn->query($sql)) {
            $_SESSION['message'] = ["type" => "success", "text" => "Objective added successfully!"];
        } else {
            $_SESSION['message'] = ["type" => "error", "text" => "Error: " . $conn->error];
        }
    } else {
        $_SESSION['message'] = ["type" => "error", "text" => "Invalid day selected."];
    }
    header("Location: dashboard.php");
    exit;
}

// Process edit objective submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    $id = (int)$_POST['id'];
    $objective = $conn->real_escape_string($_POST['objective']);
    $user_id = $_SESSION['user_id'];
    
    $sql = "UPDATE objectives SET objective = '$objective' WHERE id=$id AND user_id=$user_id";
    if ($conn->query($sql)) {
        $_SESSION['message'] = ["type" => "success", "text" => "Objective updated successfully!"];
    } else {
        $_SESSION['message'] = ["type" => "error", "text" => "Error updating objective: " . $conn->error];
    }
    header("Location: dashboard.php");
    exit;
}

// Get user's objectives grouped by day
$user_id = $_SESSION['user_id'];
$sql = "SELECT *, DAYNAME(DATE_ADD('1970-01-05', INTERVAL day_of_week-1 DAY)) as day_name 
        FROM objectives 
        WHERE user_id=$user_id 
        ORDER BY day_of_week, created_at DESC";
$result = $conn->query($sql);

// Organize objectives by day
$objectives_by_day = array_fill(1, 7, []);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $objectives_by_day[$row['day_of_week']][] = $row;
    }
}

// Handle objective status toggle
if (isset($_GET['toggle']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $sql = "UPDATE objectives SET status = 1 - status WHERE id=$id AND user_id=$user_id";
    if ($conn->query($sql)) {
        $_SESSION['message'] = ["type" => "success", "text" => "Objective status updated!"];
    } else {
        $_SESSION['message'] = ["type" => "error", "text" => "Error updating status: " . $conn->error];
    }
    header("Location: dashboard.php");
    exit;
}

// Handle objective deletion
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $sql = "DELETE FROM objectives WHERE id=$id AND user_id=$user_id";
    if ($conn->query($sql)) {
        $_SESSION['message'] = ["type" => "success", "text" => "Objective deleted!"];
    } else {
        $_SESSION['message'] = ["type" => "error", "text" => "Error deleting objective: " . $conn->error];
    }
    header("Location: dashboard.php");
    exit;
}

// Calculate progress
$total_sql = "SELECT COUNT(*) as total FROM objectives WHERE user_id=$user_id";
$complete_sql = "SELECT COUNT(*) as complete FROM objectives WHERE user_id=$user_id AND status=1";

$total_result = $conn->query($total_sql);
$complete_result = $conn->query($complete_sql);

$total = $total_result->fetch_assoc()['total'];
$complete = $complete_result->fetch_assoc()['complete'];

$percentage = $total > 0 ? round(($complete / $total) * 100) : 0;

// Get current day number (1 = Monday, 7 = Sunday)
$current_day = date('N');

renderHeader('Dashboard');
?>

<div class="min-h-screen">
    <!-- Navigation -->
    <nav class="bg-cyber-dark/90 shadow-md border-b border-cyber-pink/30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-cyber-pink animate-glow">The Loop</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-cyber-neon">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                    <a href="../logout.php" class="cyber-button inline-flex items-center px-4 py-2 rounded-md text-sm">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Progress Overview -->
        <div class="mb-8 bg-cyber-dark/80 overflow-hidden rounded-lg cyber-border backdrop-blur-sm">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-medium text-cyber-pink">Weekly Progress</h2>
                        <p class="mt-1 text-sm text-gray-300">Track your objectives completion</p>
                    </div>
                    <div class="flex items-center">
                        <div class="text-right mr-4">
                            <div class="text-2xl font-bold text-cyber-neon"><?php echo $percentage; ?>%</div>
                            <div class="text-sm text-gray-300">Completed</div>
                        </div>
                        <div class="w-16 h-16">
                            <svg class="transform -rotate-90 w-full h-full" viewBox="0 0 36 36">
                                <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                    fill="none" stroke="#333" stroke-width="3" />
                                <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                    fill="none" stroke="#FF0080" stroke-width="3"
                                    stroke-dasharray="<?php echo $percentage; ?>, 100" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Message Display -->
        <?php if(isset($_SESSION['message'])): ?>
            <div class="mb-4 rounded-md p-4 cyber-border <?php echo $_SESSION['message']['type'] === 'success' ? 'bg-cyber-dark/50 text-cyber-neon' : 'bg-cyber-dark/50 text-cyber-pink'; ?>">
                <?php echo $_SESSION['message']['text']; unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <!-- Add New Objective -->
        <div class="mb-8 bg-cyber-dark/80 overflow-hidden rounded-lg cyber-border backdrop-blur-sm">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-cyber-pink mb-4">Add New Objective</h3>
                <form method="POST" action="dashboard.php" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="objective" class="block text-sm font-medium text-cyber-neon mb-1">Objective</label>
                            <input type="text" id="objective" name="objective" required
                                   class="cyber-input w-full px-3 py-2 rounded-md"
                                   placeholder="Enter your objective...">
                        </div>
                        <div>
                            <label for="day_of_week" class="block text-sm font-medium text-cyber-neon mb-1">Day of Week</label>
                            <select id="day_of_week" name="day_of_week" required
                                    class="cyber-input w-full px-3 py-2 rounded-md">
                                <option value="1">Monday</option>
                                <option value="2">Tuesday</option>
                                <option value="3">Wednesday</option>
                                <option value="4">Thursday</option>
                                <option value="5">Friday</option>
                                <option value="6">Saturday</option>
                                <option value="7">Sunday</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" name="new_objective"
                                class="cyber-button inline-flex items-center px-4 py-2 rounded-md text-sm">
                            <i class="fas fa-plus mr-2"></i> Add Objective
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Weekly Overview -->
        <div class="bg-cyber-dark/80 rounded-lg p-6 cyber-border backdrop-blur-sm">
            <h3 class="text-lg font-medium text-cyber-pink mb-4">Weekly Overview</h3>
            <div class="space-y-6">
                <?php
                $days_of_week = [
                    1 => 'Monday',
                    2 => 'Tuesday',
                    3 => 'Wednesday',
                    4 => 'Thursday',
                    5 => 'Friday',
                    6 => 'Saturday',
                    7 => 'Sunday'
                ];

                foreach ($days_of_week as $day_num => $day_name): 
                ?>
                    <div class="relative">
                        <div class="flex items-center">
                            <h4 class="text-md font-medium <?php echo $day_num == $current_day ? 'text-cyber-pink animate-glow' : 'text-gray-300'; ?> w-32">
                                <?php echo $day_name; ?>
                                <?php if ($day_num == $current_day): ?>
                                    <span class="ml-2 text-xs inline-flex items-center justify-center px-2 py-1 rounded-full bg-cyber-pink/20 border border-cyber-pink text-cyber-pink">
                                        Today
                                    </span>
                                <?php endif; ?>
                            </h4>
                            <div class="ml-4 flex-grow border-t border-cyber-pink/30"></div>
                        </div>

                        <div class="mt-2 space-y-3">
                            <?php if (empty($objectives_by_day[$day_num])): ?>
                                <p class="text-gray-400 text-sm italic">No objectives for this day. Add one!</p>
                            <?php else: ?>
                                <?php foreach ($objectives_by_day[$day_num] as $objective): ?>
                                    <div class="objective-item bg-cyber-dark/60 p-4 rounded-lg border border-cyber-pink/20 
                                                hover:border-cyber-pink transition-all duration-300
                                                <?php echo $objective['status'] ? 'opacity-70' : ''; ?>">
                                        <div class="flex items-start justify-between">
                                            <div class="flex items-start space-x-3">
                                                <button onclick="window.location='dashboard.php?toggle=1&id=<?php echo $objective['id']; ?>'" 
                                                        class="flex-shrink-0 mt-1 h-5 w-5 rounded-full border-2 <?php echo $objective['status'] ? 'bg-cyber-neon border-cyber-neon' : 'border-cyber-pink'; ?> 
                                                        hover:shadow-neon focus:outline-none">
                                                    <?php if ($objective['status']): ?>
                                                        <svg class="h-full w-full text-cyber-dark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                        </svg>
                                                    <?php endif; ?>
                                                </button>
                                                <span class="text-sm font-medium <?php echo $objective['status'] ? 'line-through text-gray-400' : 'text-white'; ?>">
                                                    <?php echo htmlspecialchars($objective['objective']); ?>
                                                </span>
                                            </div>
                                            <div class="flex space-x-2">
                                                <button onclick="editObjective('<?php echo $objective['id']; ?>', '<?php echo addslashes(htmlspecialchars($objective['objective'])); ?>')" 
                                                        class="text-cyber-neon hover:text-cyber-pink transition-colors">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button onclick="if(confirm('Are you sure you want to delete this objective?')) window.location='dashboard.php?delete=1&id=<?php echo $objective['id']; ?>'" 
                                                        class="text-red-400 hover:text-red-500 transition-colors">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Edit Objective Modal -->
        <div id="editModal" class="fixed inset-0 bg-black bg-opacity-70 hidden items-center justify-center">
            <div class="bg-cyber-dark rounded-lg p-6 w-full max-w-md cyber-border">
                <h3 class="text-lg font-medium text-cyber-pink mb-4">Edit Objective</h3>
                <form method="POST" action="dashboard.php" id="editForm" class="space-y-4">
                    <input type="hidden" name="id" id="edit-id">
                    <div>
                        <label for="edit-objective" class="block text-sm font-medium text-cyber-neon mb-1">Objective</label>
                        <input type="text" id="edit-objective" name="objective" required
                               class="cyber-input w-full px-3 py-2 rounded-md">
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" id="cancelEdit"
                                class="inline-flex items-center px-4 py-2 border border-cyber-pink rounded-md text-sm font-medium text-cyber-pink hover:bg-cyber-pink/10 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" name="edit"
                                class="cyber-button inline-flex items-center px-4 py-2 rounded-md text-sm">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<style>
.objective-item {
    transform-origin: top;
    transition: transform 0.2s, opacity 0.2s;
}

.objective-item:hover {
    transform: translateY(-1px);
    box-shadow: 0 0 8px rgba(255, 0, 128, 0.3);
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.fade-in {
    animation: fadeIn 0.4s forwards;
}

#editModal {
    z-index: 50;
    backdrop-filter: blur(3px);
    transition: opacity 0.3s;
}
</style>

<script>
// Edit objective functionality
function editObjective(id, objective) {
    document.getElementById('edit-id').value = id;
    document.getElementById('edit-objective').value = objective;
    document.getElementById('editModal').classList.remove('hidden');
    document.getElementById('editModal').classList.add('flex');
}

document.getElementById('cancelEdit').addEventListener('click', function() {
    document.getElementById('editModal').classList.remove('flex');
    document.getElementById('editModal').classList.add('hidden');
});

// Add animation to objectives
document.addEventListener('DOMContentLoaded', function() {
    const objectives = document.querySelectorAll('.objective-item');
    objectives.forEach((item, index) => {
        item.classList.add('opacity-0');
        setTimeout(() => {
            item.classList.add('fade-in');
            item.classList.remove('opacity-0');
        }, index * 100);
    });
});
</script>

<?php renderFooter(); ?>
