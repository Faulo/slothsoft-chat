import App from "./App";
import DOM from "/slothsoft@farah/js/DOM";

try {
    const nodeList = document.querySelectorAll("*[data-chat-id='form']");
    if (nodeList.length) {
        DOM
            .loadDocumentAsync("farah://slothsoft@chat/xsl/form")
            .then(templateDoc => {
                for (const node of nodeList) {
                    try {
                        if (node.getBoundingClientRect().height) {
                            new App(node, templateDoc, false);
                        }
                    } catch (e) {
                        console.warn(e);
                    }
                }
            });
    }
} catch (e) {
    console.warn(e);
}