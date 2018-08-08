import { App } from "./ShoutboxComponents/App";
import * as Module from "/getAsset.php/slothsoft@farah/js/Module";

try {
	const nodeList = document.querySelectorAll("*[data-chat-id='form']");
	if (nodeList.length) {
		Module.resolveToDocument("farah://slothsoft@chat/xsl/form")
			.then((templateDoc) => {
				for (let i = 0; i < nodeList.length; i++) {
					let node = nodeList[i];
					try {
						if (node.getBoundingClientRect().height) {
							new App(node, templateDoc, false);
						}
					} catch(e) {
						console.log(e);
					}
				}
				return templateDoc;
			});
	}
} catch(e) {
	console.log(e);
}