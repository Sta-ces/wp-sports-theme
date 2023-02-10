/* CUSTOM JS */
load(() => {
    const MAINLOGO = getElId("main-logo")
    let lastScrollTop = 0
    action("scroll", () => {
        let st = window.pageYOffset || document.documentElement.scrollTop;
        MAINLOGO.setClassList(st > lastScrollTop, "hide-logo")
        lastScrollTop = st <= 0 ? 0 : st;
    })

    const RESEARCH = getQueries(".research")
    if(RESEARCH){
        RESEARCH.forEach(rs => {
            const PARENT = rs.parentNode
            const BUTTONS = rs.getQueries(".button")
            BUTTONS.click((e) => {
                const VAL = e.target.getAttribute("data-value")
                setResearch(VAL)
                sessionStorage.setItem("research", VAL)
                displayResearch(VAL)
            })
        
            function setResearch(target = null){
                if(!target) target = BUTTONS[0].getAttribute('data-value')
                BUTTONS.forEach(btn => btn.setClassList(btn.getAttribute("data-value") === target, "active") )
                displayResearch(target)
            }
            setResearch(sessionStorage.getItem("research"))
        
            function displayResearch(val){
                PARENT.getQueries(".research-item").forEach(item => {
                    item.setClassList(!(item.getAttribute("data-type").includes(val) || val === "all"), "none")
                    if(item.classList.contains("active") && item.classList.contains("none"))
                        item.classList.remove("active")
                })
                if(!PARENT.getQuery(".research-item.active:not(.none)"))
                    PARENT.getQuery(".research-item:not(.none)").classList.add("active")
            }
        })
    }
})

if(!Number.prototype.hasOwnProperty("between")){
    Number.prototype.between = function(a, b) {
        let min = Math.min.apply(Math, [a, b]), max = Math.max.apply(Math, [a, b])
        return this > min && this < max
    }
}