import SyntaxHighlighter from "react-syntax-highlighter";
import ExampleCode from "./ExampleCode";
import React from "react";

export default function NpmInfo() {

    return (
        <div className="box">
            <div className="title">
                <a href="https://www.npmjs.com/package/linkstrip">
                    Linkstrip
                </a>
            </div>
            <div className="subtitle">
                A small library for generating shorter links
                using the <a href="https://linkst.rip/">linkst.rip</a> API
            </div>
            <div class="is-size-4">
                Install
            </div>
            <code className="is-code">
                $ npm i linkstrip
            </code>
            <div className="is-size-4">
                Usage
            </div>
            <SyntaxHighlighter language="javascript">
                {ExampleCode}
            </SyntaxHighlighter>
            <div>
                The Linkstrip constructor can take up to 2 arguments, the url and the slug for the link to be generated, respectively. There are three methods on the Linkstrip class:
            </div>
            <div/>
            <hr/>
            <code className="is-code">
                Linkstrip.setUrl(:string)
            </code>
                sets the url for the link to be generated.
            <div/>
            <hr/>
            <code className="is-code">
                Linkstrip.setSlug(:string)
            </code>
                sets a custom slug for the linkst.rip url. Slugs must be between 5 and 100 characters, and may only contain <code className="is-code">a-z, 0-9, -_.~</code>
            <div/>
            <hr/>
            <code className="is-code">
                Linkstrip.getLinkAsync()
            </code>
                sends the url and slug info to the linkst.rip API, and returns a link if everything is acceptable. If not, an error will be thrown.
        </div>
    );
}
