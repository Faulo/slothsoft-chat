import Bootstrap from "/slothsoft@farah/js/Bootstrap";
import DOM from "/slothsoft@farah/js/DOM";
import XSLT from "/slothsoft@farah/js/XSLT";
import Client from "/slothsoft@sse/js/Client";

const sseUri = "/slothsoft@chat/data/sse";

function bootstrap() {
    const template = document.querySelector("template[*|base = 'farah://slothsoft@chat/xsl/form-range']");
    if (template) {
        const nodeList = document.querySelectorAll("*[data-chat-id = 'form']");
        for (const node of nodeList) {
            if (node.getBoundingClientRect().height) {
                new Shoutbox(node, template.content, false);
            }
        }
    }
}

export default class Shoutbox {
    constructor(formElement, templateDoc = "farah://slothsoft@chat/xsl/form", autoFocus = false) {
        this.templateDoc = templateDoc;
        this.autoFocus = autoFocus;
        this.formNode = formElement;

        this.lastId = this.formNode.getAttribute("data-chat-last-id");
        this.dbName = this.formNode.getAttribute("data-chat-database");
        this.listNode = this.formNode.querySelector("[data-chat-id='list']");
        this.inputNode = this.formNode.querySelector("[data-chat-id='input']");
        this.inputNode.value = "Initializing Server Connection...";
        this.scrollIntoView();

        this.formNode.addEventListener(
            "submit",
            (eve) => {
                eve.preventDefault();

                if (this.inputNode.value.length) {
                    this.inputNode.disabled = true;
                    this.sse.dispatchEvent(
                        "message",
                        this.inputNode.value,
                        (eve) => {
                            switch (eve.target.status) {
                                case 204:
                                    this.inputNode.disabled = false;
                                    this.inputNode.value = "";
                                    break;
                                default:
                                    console.log("Received nonstandard reply to sent message!");
                                    console.log(eve);
                                    break;
                            }
                        }
                    );
                }
            },
            false
        );

        this.sse = new Client(
            sseUri,
            this.dbName,
            this.lastId
        );
        this.sse.addEventListener(
            "start",
            (eve) => {
                this.sse.addEventListener(
                    "message",
                    (eve) => {
                        //console.log("Received message: %o", eve);
                        if (eve.data) {
                            const dataDocument = DOM.loadXML(eve.data);
                            this.lastId = DOM.evaluate("number(/range/@last-id)", dataDocument);
                            XSLT.transformToFragmentAsync(dataDocument, this.templateDoc, this.formNode.ownerDocument)
                                .then(fragment => {
                                    this.listNode.appendChild(fragment);
                                    this.scrollIntoView();
                                })
                                .catch((e) => {
                                    console.error(e);
                                });
                        }
                    });
                this.inputNode.value = "";
                this.inputNode.removeAttribute("disabled");
                if (this.autoFocus) {
                    this.inputNode.focus();
                }
            }
        );
    }
    scrollIntoView() {
        if (this.listNode.lastChild) {
            this.listNode.lastChild.scrollIntoView("smooth");
        }
        this.formNode.scrollIntoView("smooth");
    }
}

Bootstrap.run(bootstrap);