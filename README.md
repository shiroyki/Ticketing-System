# Ticketing-System
For hosting on EC2 Instance
Linux Environment: Ubuntu
Database: MySQL

# Web Contents are developed using HTML, PHP and Javascript.

# Project Features:
## Login/Register Page
- User can login to their account or continue to use the system as Guest
- Supporting Change of Password:
 Must validate the current password first 
 New password can not be the same as the current password. 
 Logout user after the password is changed
- a 2FA authentication will be required for logging in the user account
  
## Homepage
- This page display all tickets created in the ticketing system by every users
- User can use the filter to check the types of ticket they want to review

## Admin Panel Page
- Admin Actions: Delete user account, update user email, update admin flag of user
ticket management 

## Ticket Creation Page
- userid is stored in the Session for tracking the ticket created by users
  
## My Ticket Page
- It only shows the tickets created by the current logged in user

## Ticket Details Page
Comment details:
- the user id for the comment
- the creation time of the comment 


# Security technique applied in the system
- prepared statement to prevent SQL injection attack
- Implemented hidden nonce when submitting form for  preventing CSRF attack
- using hash_hmac  algorithm to hash the password for preventing Dictionary attacks
2FA authentication
- using HTTP POST method for form submission where the sensitive data cannot be cached by the browser or by intermediary servers.
