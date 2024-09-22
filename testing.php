# Battle v0.1 Web Application Use Case Table

## 1. Register

| Aspect | Details |
|--------|---------|
| Description | User creates a new account in the Battle v0.1 web application |
| Actors | User |
| Preconditions | User is not logged in and doesn't have an account |
| Postconditions | User account is created and stored in the system |
| Basic Flow | 1. User navigates to registration page
               2. User enters required information (e.g., username, email, password)
               3. System validates input
               4. System creates account
               5. System sends email OTP
               6. User enters email OTP
               7. System verifies email OTP
               8. System prompts user to create TOTP
               9. User sets up TOTP
               10. System confirms successful registration |
| Alternative Flow | - If email is already registered, system notifies user and asks for a different email
               - If email OTP is incorrect, system allows user to request a new OTP
               - If user chooses not to set up TOTP, system skips that step but recommends it for added security |

## 2. Login

| Aspect | Details |
|--------|---------|
| Description | User accesses their account in the Battle v0.1 web application |
| Actors | User |
| Preconditions | User has a registered account |
| Postconditions | User is authenticated and granted access to the application |
| Basic Flow | 1. User navigates to login page
               2. User enters credentials (username/email and password)
               3. System validates credentials
               4. System prompts for TOTP
               5. User inputs TOTP
               6. System verifies TOTP
               7. System grants access to the user's account |
| Alternative Flow | - If credentials are incorrect, system notifies user and allows retry
               - If TOTP is incorrect, system allows user to retry
               - If user has forgotten password, system provides password reset option |

## 3. Battle

| Aspect | Details |
|--------|---------|
| Description | User engages in a battle within the game |
| Actors | User |
| Preconditions | User is logged in |
| Postconditions | Battle outcome is determined and user stats are updated |
| Basic Flow | 1. User initiates a battle
               2. System matches user with an opponent (AI or another player)
               3. User and opponent take turns attacking
               4. System calculates damage and updates health for each attack
               5. Battle continues until one participant's health reaches zero
               6. System determines the winner and awards experience/items |
| Alternative Flow | - User can choose to use an item during their turn
               - User can choose to flee the battle, resulting in a loss |

## 4. View User Stat

| Aspect | Details |
|--------|---------|
| Description | User views their current game statistics |
| Actors | User |
| Preconditions | User is logged in |
| Postconditions | User's current stats are displayed |
| Basic Flow | 1. User navigates to the stats page
               2. System retrieves user's current statistics
               3. System displays statistics to the user |
| Alternative Flow | - If there's an error retrieving stats, system displays an error message and suggests trying again |

## 5. View Inventory

| Aspect | Details |
|--------|---------|
| Description | User views items in their inventory |
| Actors | User |
| Preconditions | User is logged in |
| Postconditions | User's inventory is displayed |
| Basic Flow | 1. User navigates to the inventory page
               2. System retrieves user's inventory data
               3. System displays inventory items to the user |
| Alternative Flow | - User can select an item to view more details
               - User can choose to send an item, initiating the "Send Item" use case |

## 6. View Leaderboard

| Aspect | Details |
|--------|---------|
| Description | User views the game's leaderboard |
| Actors | User |
| Preconditions | User is logged in |
| Postconditions | Leaderboard is displayed to the user |
| Basic Flow | 1. User navigates to the leaderboard page
               2. System retrieves current leaderboard data
               3. System displays leaderboard to the user |
| Alternative Flow | - User can filter leaderboard by different criteria (if available)
               - User can view their own ranking on the leaderboard |

## 7. Send Item

| Aspect | Details |
|--------|---------|
| Description | User sends an item from their inventory to another user |
| Actors | User |
| Preconditions | User is logged in and has items in their inventory |
| Postconditions | Item is transferred from user's inventory to recipient's inventory |
| Basic Flow | 1. User selects "Send Item" option in inventory
               2. User chooses item to send
               3. User enters recipient's username or email
               4. System prompts for email OTP and TOTP
               5. User enters email OTP and TOTP
               6. System verifies OTP and TOTP
               7. System transfers item to recipient's inventory
               8. System confirms successful transfer to both users |
| Alternative Flow | - If recipient doesn't exist, system notifies user
               - If OTP or TOTP is incorrect, system allows retry
               - If user cancels the transaction, item remains in user's inventory |

## 8. Use Item

| Aspect | Details |
|--------|---------|
| Description | User uses an item from their inventory during a battle |
| Actors | User |
| Preconditions | User is in an active battle and has usable items |
| Postconditions | Item is consumed and its effects are applied |
| Basic Flow | 1. User chooses to use an item during their battle turn
               2. System displays available items
               3. User selects an item to use
               4. System applies item effects
               5. System updates battle state accordingly |
| Alternative Flow | - If item use fails for any reason, user's turn is not consumed
               - User can choose to cancel item use and perform a different action |

## 9. Attack

| Aspect | Details |
|--------|---------|
| Description | User performs an attack action during a battle |
| Actors | User |
| Preconditions | User is in an active battle |
| Postconditions | Attack is resolved and damage is applied to the opponent |
| Basic Flow | 1. User chooses to attack during their battle turn
               2. System calculates attack damage based on user's stats
               3. System applies damage to the opponent
               4. System updates battle state
               5. Battle proceeds to the next turn |
| Alternative Flow | - If the attack defeats the opponent, the battle ends
               - If the user has special attacks, they can choose between different attack types |

               # Battle v0.1 Web Application Use Case Table - Addendum

## 10. Email OTP

| Aspect | Details |
|--------|---------|
| Description | System sends and user verifies an email One-Time Password (OTP) |
| Actors | User, System |
| Preconditions | User has provided a valid email address |
| Postconditions | User's email is verified |
| Basic Flow | 1. System generates a unique OTP
               2. System sends OTP to user's email
               3. User receives email and retrieves OTP
               4. User enters OTP into the application
               5. System verifies the entered OTP
               6. System confirms email verification if OTP is correct |
| Alternative Flow | - If OTP is incorrect, system allows user to retry or request a new OTP
               - If user doesn't receive the email, option to resend is provided
               - If too many incorrect attempts, system may lock the verification process temporarily |

## 11. Create TOTP

| Aspect | Details |
|--------|---------|
| Description | User sets up Time-based One-Time Password (TOTP) for two-factor authentication |
| Actors | User, System |
| Preconditions | User has registered and verified their email |
| Postconditions | TOTP is set up for the user's account |
| Basic Flow | 1. System generates a TOTP secret key
               2. System displays the secret key and QR code to the user
               3. User scans QR code with their authenticator app or enters secret key manually
               4. User enters a generated TOTP code to confirm setup
               5. System verifies the entered TOTP code
               6. System activates TOTP for the user's account |
| Alternative Flow | - If user enters incorrect TOTP code, system allows retry
               - User can choose to skip TOTP setup, but system recommends against it
               - If user loses access to their authenticator, a account recovery process should be available |

## 12. Input TOTP

| Aspect | Details |
|--------|---------|
| Description | User enters a TOTP code for authentication |
| Actors | User |
| Preconditions | User has set up TOTP and is attempting to log in or perform a sensitive action |
| Postconditions | User is authenticated with two-factor authentication |
| Basic Flow | 1. System prompts user for TOTP code
               2. User opens their authenticator app
               3. User enters the current TOTP code into the application
               4. System verifies the entered TOTP code
               5. If correct, system allows the user to proceed |
| Alternative Flow | - If TOTP code is incorrect, system allows user to try again
               - If multiple incorrect attempts, system may temporarily lock the account
               - System provides option for account recovery if user has lost access to their authenticator |

## 13. Input Email OTP and TOTP

| Aspect | Details |
|--------|---------|
| Description | User enters both Email OTP and TOTP for high-security actions (e.g., sending items) |
| Actors | User |
| Preconditions | User is logged in and attempting a high-security action |
| Postconditions | User is verified for the high-security action |
| Basic Flow | 1. System sends an OTP to user's email
               2. System prompts user for both Email OTP and TOTP
               3. User retrieves Email OTP from their email
               4. User generates TOTP from their authenticator app
               5. User enters both codes into the application
               6. System verifies both codes
               7. If both are correct, system allows the high-security action to proceed |
| Alternative Flow | - If either code is incorrect, system allows user to retry
               - System may offer to resend Email OTP if needed
               - If multiple incorrect attempts, system may temporarily block the high-security action
               - User can cancel the action at any point, returning to the previous screen |