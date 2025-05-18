<div id="loginModal"
    class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden animate-fadeIn"
    onclick="if(event.target === this) this.classList.add('hidden')">
    <div class="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center text-gray-800 tracking-wide">Login</h2>
        <form method="POST" class="space-y-6">
            <input type="hidden" name="context" value="login">
            <input type="text" name="username"
                class="w-full border border-gray-300 p-3 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-300"
                placeholder="Username" required>
            <input type="password" name="password"
                class="w-full border border-gray-300 p-3 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-300"
                placeholder="Password" required>
            <div class="flex gap-3">
                <button type="submit"
                    class="flex-1 bg-lapis-lazuli text-white p-3 rounded-lg hover:bg-blue-800 transition duration-300 font-semibold shadow-md">Login</button>
                <button type="button" onclick="document.getElementById('loginModal').classList.add('hidden')"
                    class="flex-1 mt-0 bg-gray-100 p-3 rounded-lg hover:bg-gray-200 transition duration-300">Cancel</button>
            </div>
        </form>
    </div>
</div>

<style>
@keyframes fadeIn {
    from {
        opacity: 0;
    }

    to {
        opacity: 1;
    }
}

.animate-fadeIn {
    animation: fadeIn 0.5s ease-in-out;
}
</style>