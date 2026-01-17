  </main>
  <footer class="student-footer">
    <div class="container">
      <div>© <?= date('Y') ?> Bela-Bela Institute — Student Portal</div>
      <div><a href="../index.php">Back to main site</a></div>
    </div>
  </footer>
  <script>
    // small accessibility helpers
    document.addEventListener('DOMContentLoaded', function(){
      document.querySelectorAll('.student-nav a').forEach(a=>a.addEventListener('click', ()=>{}));
    });
  </script>
</body>
</html>
