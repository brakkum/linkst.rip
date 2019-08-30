
const ExampleCode = `// frontend
import { Linkstrip } from "linkstrip";
// backend
import { NodeLinkstrip as Linkstrip } from "linkstrip";
// const Linkstrip = require("linkstrip").NodeLinkstrip;

let ls = new Linkstrip("https://linkst.rip/", "linkstrip");
ls.getLinkAsync()
  .then(link => console.log(link))
  .catch(e => console.log(e));
`

export default ExampleCode;
