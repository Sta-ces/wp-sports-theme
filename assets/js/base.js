import { ScrollMenu } from './modules.js';

load(() => {
    getQueries(".smooth-scroll, a[href^='#']").scrollSmooth();
    ScrollMenu('menu-main-content', 0);
    burgermenu(isOpen => {
        let classes = !isOpen ? ["close", "open"] : ["open", "close"];
        let menu = document.querySelector(".main-menu ul");
        if(menu.classList.contains(classes[0])) menu.classList.replace(classes[0], classes[1]);
        else menu.classList.add(classes[1]);
    });

    const YOUTUBEVIDEO = getQueries(".wp-block-embed-youtube iframe", true);
    if(YOUTUBEVIDEO.length > 0){
        YOUTUBEVIDEO.forEach(yv => {
            let srcattr = yv.setAttribute("src");
            let videoid = srcattr.replace(/^(https:\/\/www\.youtube\.com\/embed\/)/,"");
            videoid = videoid.replace(/(\?.*)?$/,"");
            yv.setAttribute("src", srcattr+"&controls=0&showinfo=0&rel=0&autoplay=1&loop=1&mute=1&playlist="+videoid);
            yv.removeAttribute("width");
            yv.removeAttribute("height");
        });
    }

    getQueries(".more-than-negative input", true).forEach(minp => minp.setAttribute('min', "0"));
});