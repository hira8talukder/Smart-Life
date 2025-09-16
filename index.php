<?php include 'header.php'; ?>

<div class="form-container">
    <h2>Customer Registration</h2>
    register.php
        <label>Username:</label>
        <input type="text" name="username" required>

        <label>Email:</label>
        <input type="email" name="email" required>

        <label>Phone Number:</label>
        <input type="tel" name="phone" pattern="[0-9]{10,15}" required>

        <label>Address:</label>
        <textarea name="address" required></textarea>

        <button type="submit">Register</button>
    </form>
</div>

<?php include 'footer.php'; ?>
