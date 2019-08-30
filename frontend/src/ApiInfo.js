import React from "react";
import "./ApiInfo.css";

export default class ApiInfo extends React.Component {

    render() {
        return(
            <div>
                <div className="box">
                    <h4 className="is-size-4 title">Available Endpoints</h4>
                    <div>
                        <div><code className="is-code">/api/checkUrl</code>Check if a url is valid</div>
                        <div><code className="is-code">/api/checkSlug</code>Check if a slug is valid/available</div>
                        <div><code className="is-code">/api/newLink</code>Create a new linkst.rip link</div>
                    </div>
                </div>
                <div className="box">
                    <h4 className="is-size-4 title">Check if you have a valid URL</h4>
                    <p>
                        Send a URL in a GET request to the checkUrl endpoint like this:
                        <hr/>
                            <code className="is-code">https://linkst.rip/api/checkUrl?<span className="is-code-highlight">url=http://some.url/</span></code>
                        <hr/>
                        A JSON response will be returned with a 'success' key. If it is false, an 'error' key will be included with a message, detailing why.
                    </p>
                </div>
                <div className="box">
                    <h4 className="is-size-4 title">Check if your slug is valid/available</h4>
                    <p>
                        Send a slug in a GET request to the checkSlug endpoint like this:
                        <hr/>
                            <code className="is-code">https://linkst.rip/api/checkSlug?<span className="is-code-highlight">slug=testSlug</span></code>
                        <hr/>
                        A JSON response will be returned with a 'success' key. It will only be true if the provided slug is both valid and available. If not, an 'error' key will be available
                    </p>
                    <p>
                        If no value is provided for slug, JSON will always return a 'success' key of true, since this means a random slug will be generated.
                    </p>
                </div>
                <div className="box">
                    <h4 className="is-size-4 title">Create new linkst.rip URL</h4>
                    <p>
                        Send a URL and optional slug in a GET request to the newLink endpoint like this:
                        <hr/>
                            <code className="is-code">https://linkst.rip/api/newLink?<span className="is-code-highlight">url=http://some.url/</span>&<span className="is-code-highlight">slug=testSlug</span></code>
                        <hr/>
                        If no slug value is given, a random string will be used for link creation.
                    </p>
                    <p>
                        If the link creation was not successful, there will be an 'error' key in the JSON with an explanation.
                    </p>
                    <p>
                        If the link was created successfully, the JSON will contain a 'url' key with the linkst.rip link.
                    </p>
                </div>
            </div>
        )
    }
}
