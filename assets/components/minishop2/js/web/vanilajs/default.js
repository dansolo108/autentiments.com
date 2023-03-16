import MiniShop from "./modules/minishop.class.js";

document.addEventListener("DOMContentLoaded",e=>{
    if (miniShop2Config) {
        window.miniShop2 = new MiniShop(miniShop2Config);
        document.dispatchEvent(new CustomEvent("miniShop2Initialize"));
    }
})
