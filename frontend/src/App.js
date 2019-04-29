import React from 'react';
import 'bulma/css/bulma.css';
import LinkstripForm from "./LinkstripForm";
import ApiInfo from "./ApiInfo";

class App extends React.Component {

    state = {
        link: "",
        linkCopied: false,
        showApiInfo: false
    };

    copyLink = () => {
        this.linkInput.select();
        document.execCommand("copy");

        this.setState({
            linkCopied: true
        });
    };

    setLink = link => {
        this.setState({
            link: link,
            linkCopied: false
        });
    };

    render() {
        const link = this.state.link;
        const showApiInfo = this.state.showApiInfo;
        return (
            <div>
                <nav className="navbar" style={{maxWidth: "900px", margin: "auto", backgroundColor: "transparent"}}>
                    <div className="navbar-brand">
                        <div className="navbar-item">
                            <a
                                className="is-size-2 is-link"
                                onClick={
                                    () => window.open("https://github.com/brakkum/linkst.rip")
                                }
                            >
                                linkst.rip
                            </a>
                        </div>
                    </div>
                    <div className="navbar-end">
                        <div className="navbar-item">
                            Long links, made easy
                        </div>
                    </div>
                </nav>
                <div style={{height: "10vh"}} />
                <section
                    className=""
                    style={{height: "100%", overflow: "auto"}}
                >
                    <div>
                        <div style={{width: "90%", maxWidth: "800px", margin: "auto"}}>
                            <div className="box" style={{backgroundColor: "#f4f4f4"}}>
                                <div className="tabs">
                                    <ul>
                                        <li
                                            className={showApiInfo ? "" : "is-active"}
                                            onClick={() => this.setState({showApiInfo: false})}
                                        >
                                            <a>Make a Link</a>
                                        </li>
                                        <li
                                            className={showApiInfo ? "is-active" : ""}
                                            onClick={() => this.setState({showApiInfo: true})}
                                        >
                                            <a>Use the API</a>
                                        </li>
                                    </ul>
                                </div>
                                {showApiInfo ?
                                    <ApiInfo />
                                    :
                                    <LinkstripForm
                                        setLink={this.setLink}
                                    />
                                }
                            </div>
                        </div>
                    </div>
                </section>
                {showApiInfo && <div style={{height: "300px"}} />}
                {link !== "" &&
                <div className="modal is-active">
                    <div className="modal-background"
                         onClick={() => this.setState({link: ""})}
                    >
                    </div>
                    <div className="modal-card" style={{width: "90%", maxWidth: "600px"}}>
                        <div className="modal-card-head">
                            New Link
                        </div>
                        <div className="modal-card-body">
                            <div className="field">
                                <input
                                    className={"input " + (this.state.linkCopied && "is-success")}
                                    defaultValue={link}
                                    ref={input => this.linkInput = input}
                                />
                            </div>
                            <div>
                                {document.queryCommandSupported('copy') &&
                                <button
                                    className="button is-success is-pulled-right"
                                    onClick={this.copyLink}
                                >
                                    Copy Link
                                </button>
                                }
                            </div>
                        </div>
                        <div className="modal-card-foot">
                            <button
                                className="button is-info is-pulled-right"
                                onClick={() => this.setState({link: ""})}
                            >
                                Close
                            </button>
                        </div>
                    </div>
                </div>
                }
            </div>
        );
    }
}

export default App;
