<div id="loginModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden animate-fadeIn" onclick="if(event.target === this) this.classList.add('hidden')">
    <div class="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-md">
        <h2 class="text-3xl font-bold mb-6 text-center text-gray-800 tracking-wide">Admin Login</h2>
        <form action="src/auth/login.php" method="POST" class="space-y-6">
            <input type="text" name="username" class="w-full border border-gray-300 p-3 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-300" placeholder="Username" required>
            <input type="password" name="password" class="w-full border border-gray-300 p-3 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-300" placeholder="Password" required>
            <button type="submit" class="w-full bg-lapis-lazuli text-white p-3 rounded-lg hover:bg-blue-800 transition duration-300 font-semibold shadow-md">Login</button>
            <button type="button" onclick="document.getElementById('loginModal').classList.add('hidden')"
                class="w-full mt-3 bg-gray-100 p-3 rounded-lg hover:bg-gray-200 transition duration-300">Cancel</button>
        </form>
    </div>
</div>

<style>
@keyframes fadeIn {
  from {opacity: 0;}
  to {opacity: 1;}
}
.animate-fadeIn {
  animation: fadeIn 0.5s ease-in-out;
}
</style>
