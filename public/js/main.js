// select elements
messageEl = document.querySelectorAll(".message");

let time = 0;

window.addEventListener("load", () => {
  if (messageEl) {
    setTimeout(() => {
      messageEl.forEach((msg) => {
        time += 5;
        setTimeout(() => {
          msg.style.display = "none";
        }, 100 * time);
      });
    }, 2000);
  }
});
