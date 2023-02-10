function burgermenu(callback = () => {}){
    const BURGER = document.querySelectorAll(".burger");
    const MAINMENU = document.getElementById('mainmenu');
    BURGER.forEach(burger => {
        if(burger.children.length <= 0) burger.insertAdjacentHTML("beforeend", "<div></div><div></div><div></div>");
        let isEvent = (burger.hasAttribute("data-event")) ? burger.getAttribute("data-event") === "true" : true
        if(isEvent){
            burger.addEventListener('click', () => {
                const TARGET = burger;
                const isOpen = TARGET.getAttribute("data-state") === "open";
                let target = isOpen ? "close" : "open";
                let doc = isOpen ? "remove" : "add";
                MAINMENU?.setAttribute("data-state", target);
                TARGET?.setAttribute("data-state", target);
                document.body.classList[doc]("no-scroll");
                callback(isOpen);
            }, false);
        }
    });
}