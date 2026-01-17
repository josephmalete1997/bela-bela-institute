
// Footer year
document.getElementById("year").textContent = new Date().getFullYear();

// Fake form submit (replace with your backend / Google Form)
const applyForm = document.getElementById("applyForm");
const formNote = document.getElementById("formNote");

if (applyForm) {
  applyForm.addEventListener("submit", (e) => {
    e.preventDefault();

    // Basic UX feedback
    const data = Object.fromEntries(new FormData(applyForm).entries());
    console.log("Application submitted:", data);

    formNote.textContent = "Thanks! Your application was received. We will contact you soon.";
    applyForm.reset();
  });
}
