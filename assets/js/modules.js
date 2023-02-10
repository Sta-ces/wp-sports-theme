/*
 * Author: Cedric Staes
 * Alias: Staces
 * URI: https://staces.be/
 * Version: 1.0
 */
export function Parallax(classname = "parallax"){
	const parallaxes = getQueries(`.${classname}`, true)
	if(parallaxes.length){
        parallaxes.map(par => {
            if(par.hasAttribute("data-src")){
                const image = par.getAttribute("data-src")
                let style = "background-image: url('"+image+"');"
                style += "background-position: 50% 0px;"
                style += "background-attachment: fixed;"
                style += "background-repeat: no-repeat;"
                style += "background-size: cover;"
                if(par.hasAttribute("style")) style += par.getAttribute("style")
                par.setAttribute("style", style)
            }
        })

		action("scroll", () => {
			const parallaxes = getQueries(`.${classname}`, true)
			if(parallaxes.length){
				const DocScroll = document.scrollingElement.scrollTop
                parallaxes.map(par => {
                    if(par.hasAttribute("data-src")){
                        const power = par.getAttribute("data-power") || 10
                        const elTop = par.closest(`.${par.getAttribute("data-parent")}`) || par
                        const position = (elTop.offsetTop - DocScroll) / power
                        par.style.backgroundPosition = "50% "+position+"px"
                    }
                })
			}
		})()
	}
}
export function Slider(classnamecontainer = "slider-container"){
    const sliders = getQueries(`.${classnamecontainer.replace('.','')}`)
    if(sliders){
        Array.from(sliders).map(slider => {
            /** INIT */
            const list_slide = Array.from(slider.getQuery(".list-slide").children).filter(ls => !ls.classList.contains("disabled"))
            const all_slide = slider.getQueries(".slider > *:not(.disabled)")
            if(!all_slide.length) slider.remove()
            const arrows = {
                left: slider.getQuery(".arrow-left"),
                right: slider.getQuery(".arrow-right")
            }
            if(!arrows.left) arrows.left = slider.appendChild(createArrow("left"))
            if(!arrows.right) arrows.right = slider.appendChild(createArrow("right"))
            let slide_active = slider.getQuery(".active")
            if(!slide_active){
                all_slide[0].classList.add("active")
                slide_active = slider.getQuery(".active")
            }
            let slide_index = Array.from(all_slide).findIndex(sl => sl.classList.contains("active"))
            let slide_width = slide_active.clientWidth
            /** END INIT */

            if(list_slide && list_slide.length){
                list_slide[slide_index].classList.add("active")
                Array.from(list_slide).map((sl, index) => {
                    sl.action("click", () => {
                        sl.classList.add("active")
                        setSlider({
                            index: index,
                            width: slide_width,
                            parent: slider
                        })
                        slide_index = loopIndex(index, all_slide.length-1)
                        resetActive(slide_index, all_slide)
                        slide_active = resetActive(index, list_slide)
                    })
                })
            }

            arrows.left.action("click", () => { arrowClick(-1) })
            arrows.right.action("click", () => { arrowClick(1) })

            function arrowClick(pos = 1){
                slide_index = loopIndex(parseInt(slide_index+pos), all_slide.length-1)
                setSlider({
                    index: slide_index,
                    width: slide_width,
                    parent: slider
                })
                slide_active = resetActive(slide_index, all_slide)
                if(list_slide) resetActive(slide_index, list_slide)
            }

            action("resize", () => {
                slide_active = slider.getQuery(".active")
                slide_width = slide_active.clientWidth
                setSlider({
                    index: slide_index,
                    width: slide_width,
                    parent: slider
                })
            })
        })
    }

    function createArrow(direction){
        let arrow = document.createElement("div")
        arrow.classList.add("arrow")
        arrow.classList.add(`arrow-${direction}`)
        return arrow
    }
    
    function loopIndex(index, max, min = 0){
        if(index < min) index = max
        if(index > max) index = min
        return index
    }
    
    function setSlider({index, width, parent}){
        let pos_slide = width * index
        parent.setAttribute("style", `--pos-slide: -${pos_slide}px`)
    }
    
    function resetActive(index, all){
        Array.from(all).filter(a => a.classList.contains("active")).map(a => a.classList.remove("active"))
        all[index].classList.add("active")
        return all[index]
    }
}
export function ScrollMenu(menuClass, offsetTop = 75){
    if(menuClass === "") return null
	const mClass = menuClass.replace(".","")

	action("scroll", scrolleffect)()
	function scrolleffect(){
		const sections = getQueries("*[data-section-scrolling]", true)
		const doc_scroll = document.scrollingElement.scrollTop

		sections.map((el, i) => {
			let menu = getQuery(`*[data-menu-scrolling='${el.getAttribute("data-section-scrolling")}']`)
            let action = (doc_scroll >= (el.offsetTop - offsetTop)) ? "add" : "remove"
			menu?.classList[action](mClass)
		})

        let allMenus = getQueries(`.${mClass}`, true)
        allMenus.pop()
        if(allMenus.length) allMenus.map(am => am.classList.remove(mClass))
	}
}
export function Cloner(content, parent, position = "after"){
    Array.from(parent).map( p => {
        const contentNode = document.importNode(content, true)
        switch(position){
            case "before": p.insertBefore(contentNode, p.firstChild); break;
            case "after": default: p.appendChild(contentNode); break;
        }
    } )
}
export class Timer {
    constructor(callback, time = 1000){
        this.time = time
        this.callback = callback
        this.timerObject = setInterval(callback, time)
    }

    stop() {
        if (this.timerObject) {
            clearInterval(this.timerObject)
            this.timerObject = null
        }
        return this
    }

    start() {
        if (!this.timerObject) {
            this.stop()
            this.timerObject = setInterval(this.callback, this.time)
        }
        return this
    }

    reset(newTime = this.time) {
        this.time = newTime
        return this.stop().start()
    }

    isStart(){ return this.timerObject !== null }
}
export function getSymbols(onString = false){
    let symbols = [ "&","#","@","(",")","§","!",
                    "{","}","-","_","*","$","µ",
                    "%","£","€","<",">","\\",";",
                    ",",".","?",":","/","+","=",
                    "|","[","]"]
    if(onString) return symbols.join('')
    return symbols
}
export function AutoTyped({ parents, strings, typeSpeed = 10, waiting = 20 }) {
    if (!strings.length || !parents.length) return null

    typeSpeed *= 100; waiting *= 100

    Array.from(parents).map(parent => {
        let state = 'default'
        let timer = new Timer(() => {
            let textLength = parent.textContent.length
            switch (state) {
                case 'erased':
                    if (parent.textContent.length)
                        parent.textContent = parent.textContent.substr(0, textLength - 1)
                    else {
                        state = 'default';
                        strings.push(strings.shift())
                    }
                    break;
                case 'typed':
                    if (textLength < strings[0].length)
                        parent.innerHTML += strings[0].charAt(textLength)
                    else state = 'default'
                    break;
                default:
                    let timerSystem = new Timer(() => {
                        state = (parent.textContent.length) ? 'erased' : 'typed'
                        timer.start()
                        timerSystem.stop()
                    }, waiting);
                    timer.stop()
                    break;
            }
        }, typeSpeed);
    })
}