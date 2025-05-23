document.addEventListener("DOMContentLoaded", function () {
    const showNavbar = (toggleId, navId, bodyId, headerId) => {
        const toggle = document.getElementById(toggleId),
            nav = document.getElementById(navId),
            bodypd = document.getElementById(bodyId),
            headerpd = document.getElementById(headerId);

        if (toggle && nav && bodypd && headerpd) {
            toggle.addEventListener("click", () => {
                nav.classList.toggle("show");
                toggle.classList.toggle("bx-x");
                bodypd.classList.toggle("body-pd");
                headerpd.classList.toggle("body-pd");
            });
        }
    };

    showNavbar("header-toggle", "nav-bar", "body-pd", "header");

    /*===== LINK ACTIVE STATE =====*/
    const linkColor = document.querySelectorAll(".nav_link");

    function colorLink() {
        linkColor.forEach((l) => l.classList.remove("active"));
        this.classList.add("active");
    }

    linkColor.forEach((l) => l.addEventListener("click", colorLink));

    /*===== SMOOTH DROPDOWN TRANSITION =====*/
    document.querySelectorAll(".dropdown-toggle").forEach((item) => {
        item.addEventListener("click", function () {
            let nextEl = this.nextElementSibling;

            if (nextEl && nextEl.classList.contains("nav_dropdown")) {
                if (nextEl.classList.contains("show")) {
                    nextEl.style.maxHeight = "0px";
                    setTimeout(() => nextEl.classList.remove("show"), 300);
                } else {
                    nextEl.classList.add("show");
                    nextEl.style.maxHeight = nextEl.scrollHeight + "px";
                }
            }
        });
    });
});
