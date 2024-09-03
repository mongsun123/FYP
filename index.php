<?php
session_start();
$userId = $_SESSION['user_id'];

// Set session timeout duration (in seconds)
$timeout_duration = 600; // 10 minutes
// Check if the "last_activity" session variable is set
if (isset($_SESSION['last_activity'])) {
    // Calculate the session's lifetime
    $elapsed_time = time() - $_SESSION['last_activity'];
    
    // If the elapsed time exceeds the timeout duration, destroy the session and redirect to login
    if ($elapsed_time > $timeout_duration) {
        session_unset();
        session_destroy();
        header("Location: login.php?timeout=true");
        exit();
    }
}

// Update "last_activity" to the current time
$_SESSION['last_activity'] = time();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

?>

<style>
    body, html {
        margin: 0;
        padding: 0;
        height: 100%;
        font-family: 'Exo', sans-serif;
        color: #fff;
        background-color: #2a2a2a; /* Dark background color */
    }

    .ui-container {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100vh; /* Full height */
        padding: 20px;
        box-sizing: border-box;
    }

    .top-section, .bottom-section {
        background: rgba(0, 0, 0, 0.8); /* Semi-transparent background with color */
        padding: 10px;
        border-radius: 10px;
        text-align: center;
        box-shadow: 0px 0px 20px 5px rgba(0,0,0,0.5);
    }

    .top-section {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .welcome-message {
        font-size: 1.5em;
        text-shadow: 0px 0px 10px #ff6f00;
    }

    .logout-button {
        padding: 10px;
        font-size: 1em;
        border: none;
        border-radius: 5px;
        background: linear-gradient(45deg, #ff6f00, #ff8e53);
        color: #fff;
        cursor: pointer;
        transition: 0.3s;
        box-shadow: 0px 0px 10px 2px #ff6f00;
    }

    .logout-button:hover {
        box-shadow: 0px 0px 20px 5px #ff8e53;
    }

    .choice-layer, .content-layer, .inventory-layer {
        padding: 20px;
        border-radius: 10px;
        background: rgba(42, 42, 42, 0.8);
        box-shadow: 0px 0px 20px 5px rgba(0,0,0,0.5);
    }

    .choice-layer {
        width: 20%; /* Adjust as needed */
        border-right: 2px solid #000;
        display: flex;
        flex-direction: column;
    }

    .content-layer {
        width: 60%; /* Adjust as needed */
        overflow-y: auto;
    }

    .inventory-layer {
        width: 20%; /* Adjust as needed */
        border-left: 2px solid #000;
        display: flex;
        flex-direction: column;
    }

    .advice-text {
        font-size: 1.2em;
        text-shadow: 0px 0px 10px #ff6f00;
        animation: fade 1s ease-in-out infinite;
    }

    .menu-title {
        padding: 10px;
        font-size: 1.5em;
        text-align: center;
        background-color: #333; /* You can change this to match your theme */
        border-radius: 5px;
        margin-bottom: 10px; /* Space between the title and the buttons */
        box-shadow: 0px 0px 10px 2px rgba(0,0,0,0.5);
        text-shadow: 0px 0px 10px #ff6f00;
    }

    .menu-button {
        width: 100%;
        padding: 15px;
        margin-bottom: 10px;
        font-size: 1.2em;
        color: #fff;
        background-color: #444; /* Button background color */
        border: none;
        border-radius: 5px;
        text-align: center;
        cursor: pointer;
        transition: background-color 0.3s ease;
        box-shadow: 0px 0px 10px 2px rgba(0,0,0,0.5);
    }

    .menu-button:hover {
        background-color: #555; /* Button hover effect */
    }
    
    .inventory-table {
        width: 100%;
        max-width: 100%; /* Set a maximum width for all tables */
        margin-bottom: 20px;
        border-collapse: collapse;
    }

    .inventory-table th, .inventory-table td {
        padding: 8px;
        text-align: left;
        border: 1px solid #ddd;
    }

    @keyframes fade {
        0%, 100% { opacity: 0; }
        50% { opacity: 1; }
    }
    
</style>

<div class="ui-container">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <div class="top-section">
        <div class="welcome-message">Welcome back, <?php echo $_SESSION['username']; ?>!</div>
        <form action="logout.php" method="post">
            <button type="submit" class="logout-button">Logout</button>
        </form>
    </div>

    <div class="main-content" style="display: flex; flex: 1; " >
        <div class="choice-layer">
            <div id="menu-title" class="menu-title">Main Menu</div>
            <button class="menu-button" id='battleBtn' onclick="changeMenu('Battle')">Battle</button>
            <button class="menu-button" id='userStatBtn'onclick="changeMenu('User Stat')">User Stat</button>
            <button class="menu-button" id='inventoryBtn'onclick="changeMenu('Inventory')">Inventory</button>
            <!-- Battle -->
            <button class="menu-button" id='easyBtn'onclick="changeMenu('Easy')" style="display: none;">Easy</button>
            <button class="menu-button" id='mediumBtn'onclick="changeMenu('Medium')" style="display: none;">Medium</button>
            <button class="menu-button" id='hardBtn'onclick="changeMenu('Hard')" style="display: none;">Hard</button>
            <button class="menu-button" id='InsaneBtn'onclick="changeMenu('Insane')" style="display: none;">Insane</button>
            
            <button class="menu-button" id='attackBtn'onclick="changeMenu('Attack')" style="display: none;">Attack</button>
            <button class="menu-button" id='useItemBtn'onclick="changeMenu('Use Item')" style="display: none;">Use Item</button>
            <button class="menu-button" id='quitBtn'onclick="changeMenu('Back')" style="display: none;">Quit to Menu</button>
            <!-- User Stat -->
            <!-- Inventory -->
            <button class="menu-button" id='sendItemBtn'onclick="changeMenu('Send Item')" style="display: none;">Send Item</button>
            <button class="menu-button" id='backBtn'onclick="changeMenu('Back')" style="display: none;">Back</button>
        </div>

        <div class="content-layer" id="content-layer">
            <!-- Main game content goes here -->
            <p>You enter the dark forest. The trees seem to whisper around you. A goblin appears in the clearing ahead.</p>
        </div>
        
        <div class="inventory-layer">
            <h2>Your Inventory</h2>
            <ul id="inventory-list">
                <!-- Inventory items will be inserted here by JavaScript -->
            </ul>   
        </div>
    </div>

    <div class="bottom-section">
        <p class="advice-text" id="cyber-advice"></p>
    </div>
</div>

<script>
    const playerId = <?php echo json_encode($_SESSION['user_id']); ?>;
    let easyMaxHp = 100;
    let easyhp = easyMaxHp;
    let easyattack = 5;
    let easydefense = 5;

    let mediumMaxHp = 150;
    let mediumhp = mediumMaxHp;
    let mediumattack = 10;
    let mediumdefense = 10;

    let hardMaxHp = 200;
    let hardhp = hardMaxHp;
    let hardattack = 15;
    let harddefense = 15;

    let insaneMaxHp = 300;
    let insanehp = insaneMaxHp;
    let insaneattack = 25;
    let insanedefense = 25;

    let enemyMaxHp;  // Maximum HP for easy level enemy
    let enemyHp;  // Current HP for easy level enemy
    let enemyAttack;
    let enemyDefense;
    
    let playerMaxHp = 100;  // Maximum HP for the player
    let playerHp = playerMaxHp;  // Current HP for the player
    let level = 5;
    let attack = 10;
    let defense = 10;
    let exp = 5;
    let expRequired = 100;

    function changeMenu(menuName) {
        // Change content based on the selected menu
        let content = '';
        if (menuName === 'Battle') {
            document.getElementById('menu-title').textContent = menuName;
            document.getElementById('battleBtn').style.display = 'none';
            document.getElementById('userStatBtn').style.display = 'none';
            document.getElementById('inventoryBtn').style.display = 'none';
            
            document.getElementById('easyBtn').style.display = '';
            document.getElementById('mediumBtn').style.display = '';
            document.getElementById('hardBtn').style.display = '';
            document.getElementById('InsaneBtn').style.display = '';
            document.getElementById('backBtn').style.display = '';
            content = '<p>You prepare your weapon and get ready to fight.</p>';
        } else if (menuName === 'User Stat') {
            document.getElementById('menu-title').textContent = menuName;
            content = `<p>Your stats show your current level and abilities.</p>
                        <p>Level: ${level}</p>
                        <p>HP: ${playerMaxHp}</p>
                        <p>Attack: ${attack}</p>
                        <p>Defense: ${defense}</p>
                        <p>Exp: ${exp} / ${expRequired}</p>`;
        } else if (menuName === 'Inventory') {
            document.getElementById('menu-title').textContent = menuName;
            document.getElementById('battleBtn').style.display = 'none';
            document.getElementById('userStatBtn').style.display = 'none';
            document.getElementById('inventoryBtn').style.display = 'none';
            
            document.getElementById('sendItemBtn').style.display = '';
            document.getElementById('backBtn').style.display = '';
            content = '<p>You open your inventory and check your items.</p>';

            // Fetch inventory data from the server
            fetch('get_inventory.php')
            .then(response => response.json())
            .then(data => {
                let content = '<h2>Your Inventory</h2>';
                let levels = {1: [], 2: [], 3: [], 4: []}; // To categorize items by levels
            
                // Categorize items by level
                data.forEach(item => {
                    if (levels[item.item_level]) {
                        levels[item.item_level].push(item);
                    }
                });
            
                // Construct the HTML content as a table
                for (let level in levels) {
                    if (levels[level].length > 0) {
                        content += `<h3>Level ${level} Items</h3>`;
                        content += `<table class="inventory-table">
                                      <thead>
                                        <tr>
                                          <th style="width: 20%;">Item Name</th>
                                          <th style="width: 40%;">Description</th>
                                          <th style="width: 10%;">Type</th>
                                          <th style="width: 10%;">Value</th>
                                          <th style="width: 10%;">Effect</th>
                                          <th style="width: 10%;">Quantity</th>
                                        </tr>
                                      </thead>
                                      <tbody>`;
                    
                        // Add rows for each item in the level
                        levels[level].forEach(item => {
                            content += `<tr>
                                          <td style="width: 20%;">${item.item_name}</td>
                                          <td style="width: 40%;">${item.item_description}</td>
                                          <td style="width: 10%;">${item.item_type}</td>
                                          <td style="width: 10%;">${item.item_value}</td>
                                          <td style="width: 10%;">${item.item_effect}</td>
                                          <td style="width: 10%;">${item.quantity}</td>
                                        </tr>`;
                        });
                    
                        content += `</tbody></table>`;
                    }
                }
            
                document.getElementById('content-layer').innerHTML = content; // Display the content as a table
            })
            .catch(error => {
                console.error('Error fetching inventory data:', error);
            });
        } else if (menuName === 'Easy') {
            document.getElementById('menu-title').textContent = menuName;   

            enemyMaxHp = 100;
            enemyHp = enemyMaxHp;
            enemyAttack = 15;
            enemyDefense = 5;

            // Hide difficulty buttons and show action buttons
            battleUi();

            content = '<p>Enemy HP: ' + enemyHp + '</p>' +
                      '<p>Enemy Attack Power: ' + enemyAttack + '</p>' +
                      '<p>Enemy Defense Power: ' + enemyDefense + '</p>' +
                      '<p>Your HP: ' + playerHp + '</p>' +
                      '<p>Your Attack Power: ' + attack + '</p>' +
                      '<p>Your Attack Power: ' + defense + '</p>' +
                      '<p>Choose your action:</p>'
            //content = '<p>Your stats show your current level and abilities.</p>';
        } else if (menuName === 'Medium') {
            document.getElementById('menu-title').textContent = menuName;
            
            enemyMaxHp = 150;
            enemyHp = enemyMaxHp;
            enemyAttack = 25;
            enemyDefense = 10;

            battleUi();

            content = '<p>Enemy HP: ' + enemyHp + '</p>' +
                      '<p>Enemy Attack Power: ' + enemyAttack + '</p>' +
                      '<p>Enemy Defense Power: ' + enemyDefense + '</p>' +
                      '<p>Your HP: ' + playerHp + '</p>' +
                      '<p>Your Attack Power: ' + attack + '</p>' +
                      '<p>Your Attack Power: ' + defense + '</p>' +
                      '<p>Choose your action:</p>'
        } else if (menuName === 'Hard') {
            document.getElementById('menu-title').textContent = menuName;
            
            enemyMaxHp = 200;
            enemyHp = enemyMaxHp;
            enemyAttack = 40;
            enemyDefense = 15;

            battleUi();

            content = '<p>Enemy HP: ' + enemyHp + '</p>' +
                      '<p>Enemy Attack Power: ' + enemyAttack + '</p>' +
                      '<p>Enemy Defense Power: ' + enemyDefense + '</p>' +
                      '<p>Your HP: ' + playerHp + '</p>' +
                      '<p>Your Attack Power: ' + attack + '</p>' +
                      '<p>Your Attack Power: ' + defense + '</p>' +
                      '<p>Choose your action:</p>'
        } else if (menuName === 'Insane') {
            document.getElementById('menu-title').textContent = menuName;
            
            enemyMaxHp = 300;
            enemyHp = enemyMaxHp;
            enemyAttack = 60;
            enemyDefense = 40;

            battleUi();

            content = '<p>Enemy HP: ' + enemyHp + '</p>' +
                      '<p>Enemy Attack Power: ' + enemyAttack + '</p>' +
                      '<p>Enemy Defense Power: ' + enemyDefense + '</p>' +
                      '<p>Your HP: ' + playerHp + '</p>' +
                      '<p>Your Attack Power: ' + attack + '</p>' +
                      '<p>Your Attack Power: ' + defense + '</p>' +
                      '<p>Choose your action:</p>'
        } else if (menuName === 'Send Item') {
            document.getElementById('menu-title').textContent = menuName;
            content = `
                <p>Search for a user to send an item to:</p>
                <input type="text" id="search-input" placeholder="Enter username or user ID">
                <button onclick="searchUser()">Search</button>
                <div id="search-results"></div>
                <div id="send-item-form" style="display:none;">
                    <h3>Send Item</h3>
                    <input type="hidden" id="selected-user-id">
                    <label for="item-select">Choose an item to send:</label>
                    <select id="item-select"></select>
                    <button onclick="sendItem()">Send Item</button>
                </div>
            `;

            document.getElementById('content-layer').innerHTML = content;
        } else if (menuName === 'Back') {
            resetStats();

            document.getElementById('battleBtn').style.display = '';
            document.getElementById('userStatBtn').style.display = '';
            document.getElementById('inventoryBtn').style.display = '';
            
            document.getElementById('easyBtn').style.display = 'none';
            document.getElementById('mediumBtn').style.display = 'none';
            document.getElementById('hardBtn').style.display = 'none';
            document.getElementById('InsaneBtn').style.display = 'none';
            document.getElementById('attackBtn').style.display = 'none';
            document.getElementById('useItemBtn').style.display = 'none';
            document.getElementById('quitBtn').style.display = 'none';
            document.getElementById('backBtn').style.display = 'none';
            document.getElementById('sendItemBtn').style.display = 'none';
            
            content = '<p>Back to Main Menu.</p>';
            document.getElementById('menu-title').textContent = 'Main Menu';
        }else if (menuName === 'Attack') {
            // Get player's level (this could be stored in a variable or retrieved from the database/session)
            let playerLevel;
            console.log(enemyMaxHp);
            if(enemyMaxHp == 100){
                playerLevel = 1; // Example level, replace with actual level retrieval logic
            }else if(enemyMaxHp == 150){
                playerLevel = 2;
            }else if(enemyMaxHp == 200){
                playerLevel = 3;
            }else if(enemyMaxHp == 300){
                playerLevel = 4;
            }

            // Get items for the current level
            const itemsForLevel = getAllItemsByLevel(playerLevel);

            // Player attacks
            let damageToEnemy = attack - enemyDefense;
            if (damageToEnemy < 0) damageToEnemy = 0;
            enemyHp -= damageToEnemy;

            content = '<p>You attacked the enemy!</p>' +
                      '<p>Enemy HP remaining: ' + enemyHp + '</p>';
            
            // Check if enemy is defeated
            if (enemyHp <= 0) {
                content += '<p>You defeated the enemy!</p>';
                document.getElementById('attackBtn').style.display = 'none';
                document.getElementById('useItemBtn').style.display = 'none';

                const selectedItem = getRandomItem(itemsForLevel);
                // Assign items to player based on the level
                content += '<p>You found the following item:</p>';
                content += `<p>Item: ${selectedItem}</p>`;

                addItemToInventory(playerId, selectedItem);
                loadInventory();
            } else {
                // Enemy's turn to attack
                let missChance = Math.random();
                if (missChance > 0.1) {  // 90% chance to hit
                    let damageToPlayer = enemyAttack - defense;
                    if (damageToPlayer < 0) damageToPlayer = 0;
                    playerHp -= damageToPlayer;

                    content += '<p>The enemy attacks you!</p>' +
                               '<p>Your HP remaining: ' + playerHp + '</p>';
                } else {
                    content += '<p>The enemy missed their attack!</p>';
                }

                // Check if the player is defeated
                if (playerHp <= 0) {
                    content += '<p>You were defeated by the enemy!</p>';
                    document.getElementById('attackBtn').style.display = 'none';
                    document.getElementById('useItemBtn').style.display = 'none';
                }
            }
        }else if (menuName === 'Defense') {
            content = '<p>You prepare to defend!</p>';
        }
        document.getElementById('content-layer').innerHTML = content;
    }

    function resetStats() {
        playerHp = playerMaxHp;
        enemyHp = enemyMaxHp;
    }

    function getRandomItem(items) {
        return items[Math.floor(Math.random() * items.length)];
    }

    function addItemToInventory(playerId, itemName) {
        // Example: Use AJAX to send data to a PHP script for inserting into the database
        fetch('add_item_to_inventory.php', {
            method: 'POST',
            body: JSON.stringify({ playerId, itemName }),
            headers: { 'Content-Type': 'application/json' }
        }).then(response => response.json())
        .then(data => {
            console.log(data.message);
            
            // After successfully adding the item to the database, update the inventory in the HTML
            if (data.success) {
                updateInventoryDisplay(itemName);
            }
        })
        .catch(error => console.error('Error:', error));
    }

    function updateInventoryDisplay(itemName) {
        const inventoryList = document.getElementById('inventory-list');
        const newItem = document.createElement('li');
        newItem.textContent = itemName;
        inventoryList.appendChild(newItem);
    }

    // Function to get items based on level
    function getAllItemsByLevel(level) {
        const items = {
            1: ["Wooden Sword", "Iron Sword", "Leather Armor", "Iron Armor", "Health Potion", "Attack Potion", "Defense Potion"],
            2: ["Steel Sword", "Golden Sword", "Chain Mail", "Steel Armor", "Greater Health Potion", "Greater Attack Potion", "Greater Defense Potion"],
            3: ["Dragon Sword", "Magic Sword", "Dragon Mail", "Magic Armor", "Superior Health Potion", "Superior Attack Potion", "Superior Defense Potion"],
            4: ["Excalibur", "Holy Sword", "Holy Armor", "Dragon Scale Armor", "Ultimate Health Potion", "Ultimate Attack Potion", "Ultimate Defense Potion"]
        };
        return items[level] || []; // Return items for the given level or an empty array if level is not matched
    }

    function searchUser() {
        const searchInput = document.getElementById('search-input').value;
        fetch('search_user.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ search: searchInput }),
        })
        .then(response => response.json())
        .then(users => {
            let resultsHtml = '<ul>';
            users.forEach(user => {
                resultsHtml += `<li>
                    <a href="#" onclick="selectUser(${user.id}, '${user.username}')">${user.username} (ID: ${user.id})</a>
                </li>`;
            });
            resultsHtml += '</ul>';
            document.getElementById('search-results').innerHTML = resultsHtml;
        })
        .catch(error => {
            console.error('Error searching user:', error);
        });
    }
    
    function selectUser(userId, username) {
        document.getElementById('send-item-form').style.display = 'block';
        document.getElementById('selected-user-id').value = userId;
        document.getElementById('search-results').innerHTML = `<p>Selected User: ${username} (ID: ${userId})</p>`;
        populateItemSelect();
    }
    
    function populateItemSelect() {
        fetch('get_items.php')
        .then(response => response.json())
        .then(items => {
            const itemSelect = document.getElementById('item-select');
            itemSelect.innerHTML = '';
            items.forEach(item => {
                itemSelect.innerHTML += `<option value="${item.id}">${item.item_name}</option>`;
            });
        })
        .catch(error => {
            console.error('Error fetching items:', error);
        });
    }
    
    function sendItem() {
        const userId = document.getElementById('selected-user-id').value;
        const itemName = document.getElementById('item-select').value;
        
        fetch('send_item.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ userId: userId, itemName: itemName }),
        })
        .then(response => response.json())
        .then(result => {
            Swal.fire({
                title: result.status === 'success' ? 'Success' : 'Error',
                text: result.message,
                icon: result.status === 'success' ? 'success' : 'error',
            }).then(() => {
                if (result.status === 'success') {
                    document.getElementById('send-item-form').style.display = 'none';
                    document.getElementById('search-results').innerHTML = '';
                }
            });
        })
        .catch(error => {
            console.error('Error sending item:', error);
        });
    }

    function battleUi(){
        document.getElementById('easyBtn').style.display = 'none';
        document.getElementById('mediumBtn').style.display = 'none';
        document.getElementById('hardBtn').style.display = 'none';
        document.getElementById('InsaneBtn').style.display = 'none';
        document.getElementById('backBtn').style.display = 'none';

        document.getElementById('attackBtn').style.display = '';
        document.getElementById('useItemBtn').style.display = '';
        document.getElementById('quitBtn').style.display = '';
    }

    const advices = [
        "Keep your password updated.",
        "Use strong, unique passwords.",
        "Beware of phishing scams.",
        "Enable two-factor authentication.",
        "Regularly back up your data."
    ];
    let adviceIndex = 0;

    function showNextAdvice() {
        document.getElementById("cyber-advice").textContent = advices[adviceIndex];
        adviceIndex = (adviceIndex + 1) % advices.length;
    }

    // Show the first advice and then rotate every 10 seconds
    showNextAdvice();
    setInterval(showNextAdvice, 10000);


    function loadInventory() {
        fetch('get_inventory.php')
            .then(response => response.json())
            .then(data => {
                const inventoryList = document.getElementById('inventory-list');
                inventoryList.innerHTML = ''; // Clear the list
            
                data.forEach(item => {
                    const li = document.createElement('li');
                    li.textContent = `${item.item_name} (x${item.quantity})`;
                    inventoryList.appendChild(li);
                });
            })  
            .catch(error => console.error('Error fetching inventory:', error));
    }
    
    // Call this function when the inventory menu is opened
    loadInventory();
</script>
