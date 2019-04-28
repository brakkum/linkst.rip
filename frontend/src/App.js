import React from 'react';
import 'bulma/css/bulma.css';
import LinkstripForm from "./LinkstripForm";

class App extends React.Component {

    state = {
        link: "",
        linkCopied: false
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
        return (
            <div style={{backgroundColor: "transparent"}}>
                <nav className="navbar" style={{maxWidth: "900px", margin: "auto", backgroundColor: "transparent"}}>
                    <div className="navbar-brand">
                        <div className="navbar-item">
                            <a
                                className="is-size-2 is-link"
                                onClick={
                                    () => window.open("https://github.com/brakkum/linkst.rip")
                                }
                            >
                                Linkst.rip
                            </a>
                        </div>
                    </div>
                    <div className="navbar-end">
                        <div className="navbar-item">
                            Long links, made easy
                        </div>
                    </div>
                </nav>
                <div
                    style={{height: "10vh"}}
                />
                <div
                    className="is-hidden-mobile"
                    style={{height: "15vh"}}
                />
                <div
                    className="hero is-fullheight-with-navbar"
                >
                    <div>
                        <div style={{width: "90%", maxWidth: "800px", margin: "auto"}}>
                            <LinkstripForm
                                setLink={this.setLink}
                            />
                        </div>
                    </div>
                </div>
                {link !== "" &&
                    <div className="modal is-active">
                        <div className="modal-background"
                            onClick={() => this.setState({link: ""})}
                        >
                        </div>
                        <div className="modal-card" style={{width: "90%"}}>
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
