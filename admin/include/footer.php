<script>
  lucide.createIcons();

  // Set the initial theme based on localStorage
  document.addEventListener("DOMContentLoaded", () => {
    if (localStorage.getItem("theme") === "dark") {
      document.documentElement.classList.add("dark");
    } else {
      document.documentElement.classList.remove("dark");
    }
  });

  // Sidebar toggle
  document.getElementById('menu-btn').addEventListener('click', () => {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('hidden');
  });

  // Theme toggle
  document.getElementById('theme-toggle').addEventListener('click', () => {
    // Toggle the theme on the document root
    document.documentElement.classList.toggle('dark');

    // Save the current theme to localStorage
    if (document.documentElement.classList.contains('dark')) {
      localStorage.setItem("theme", "dark");
    } else {
      localStorage.setItem("theme", "light");
    }
  });
</script>