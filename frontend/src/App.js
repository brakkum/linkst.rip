import React from 'react';
import 'bulma/css/bulma.css';
import LinkstripForm from "./LinkstripForm";
import ApiInfo from "./ApiInfo";
import NpmInfo from './NpmInfo';

class App extends React.Component {

    state = {
        link: "",
        linkCopied: false,
        openTab: "link", // "link" | "api" | "npm"
        background: ""
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

    getAppBackground = () => {
        const randHex = (min = 0, max = 255) => {
            return "#000000".replace(/00/g, () => {
                return (~~(Math.random() * (max - min) + min)).toString(16);
            });
        };

        const lighterShade = randHex(215, 255);
        const darkerShade = randHex(100, 175);
        const randDegrees = Math.random() * (360 - 220) + 220;

        return `linear-gradient(${randDegrees}deg, ${darkerShade}, ${lighterShade})`;
    };

    componentDidMount = () => {
        this.setState({background: this.getAppBackground()});
    };

    render() {
        const link = this.state.link;
        const openTab = this.state.openTab;
        return (
            <div>
                <div id="app-background" style={{background: this.state.background}}></div>
                <nav className="navbar" style={{maxWidth: "900px", margin: "auto", backgroundColor: "transparent"}}>
                    <div className="navbar-brand">
                        <div className="navbar-item">
                            <a
                                className="is-size-2 is-link"
                                href="https://github.com/brakkum/linkst.rip"
                                target="_blank"
                                rel="noopener noreferrer"
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
                <section>
                    <div>
                        <div style={{width: "90%", maxWidth: "800px", margin: "10px auto"}}>
                            <div className="box" style={{backgroundColor: "#f4f4f4"}}>
                                <div className="tabs">
                                    <ul>
                                        <li
                                            className={openTab === "link" ? "is-active" : ""}
                                            onClick={() => this.setState({openTab: "link"})}

                                        >
                                            <a href="#link">Make a Link</a>
                                        </li>
                                        <li
                                            className={openTab === "api" ? "is-active" : ""}
                                            onClick={() => this.setState({openTab: "api"})}
                                        >
                                            <a href="#api">Use the API</a>
                                        </li>
                                        <li
                                            className={openTab === "npm" ? "is-active" : ""}
                                            onClick={() => this.setState({openTab: "npm"})}
                                        >
                                            <a href="#npm">npm</a>
                                        </li>
                                    </ul>
                                </div>
                                {openTab === "link" ?
                                    <LinkstripForm
                                        setLink={this.setLink}
                                    />
                                    :
                                    openTab === "api" ?
                                        <ApiInfo />
                                    :
                                    openTab === "npm" ?
                                        <NpmInfo />
                                    :
                                    ""
                                }
                            </div>
                        </div>
                    </div>
                </section>
                <div style={{height: "50px"}} />
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
