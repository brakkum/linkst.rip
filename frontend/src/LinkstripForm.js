import React from "react";

class LinkstripForm extends React.Component {

    _timeoutLength = 500;

    state = {
        url: "",
        urlTimeout: 0,
        urlValid: false,
        slug: "",
        slugTimeout: 0,
        slugValid: true,
        error: "",
        sendingRequest: false
    };

    handleUrlChange = e => {
        e.preventDefault();
        let url = e.target.value;

        if (this.state.urlTimeout) {
            clearTimeout(this.state.urlTimeout);
        }

        this.setState({
            url: url,
            urlTimeout: setTimeout(() => {
                fetch(`/api/checkUrl?url=${url}`)
                    .then(res => res.json())
                    .then(json => {
                        if (json.success) {
                            this.setState({
                                urlValid: true,
                                error: ""
                            });
                        } else {
                            this.setState({
                                urlValid: false,
                                error: json.error
                            })
                        }
                    }).catch(e => console.log(e));
            }, this._timeoutLength)
        });
    };

    handleSlugChange = e => {
        e.preventDefault();
        let slug = e.target.value;

        if (this.state.slugTimeout) {
            clearTimeout(this.state.slugTimeout);
        }

        if (slug === "") {
            this.setState({
                slug: "",
                slugValid: true,
                slugTimeout: 0
            });
        }

        this.setState({
            slug: slug,
            slugTimeout: setTimeout(() => {
                fetch(`${this._host}/api/checkSlug?slug=${slug}`)
                    .then(res => res.json())
                    .then(json => {
                        if (json.success) {
                            this.setState({
                                slugValid: true,
                                error: ""
                            });
                        } else {
                            this.setState({
                                slugValid: false,
                                error: json.error
                            })
                        }
                    }).catch(e => console.log(e));
            }, this._timeoutLength)
        });
    };

    submitLink = () => {
        if (!this.state.urlValid || !this.state.slugValid) {
            return;
        }

        let url = this.state.url;
        let slug = this.state.slug;

        fetch(`/api/newLink?url=${url}&slug=${slug}`)
            .then(res => res.json())
            .then(json => {
                if (json.success) {
                    this.setState({
                        url: "",
                        slug: ""
                    });
                    this.props.setLink(json.url);
                } else {
                    this.setState({error: json.error});
                }
            }).catch(e => console.log(e));
    };

    render() {
        return(
            <div>
                <div className="field">
                    <label className="label">
                        Url
                    </label>
                    <input
                        placeholder="https://linkst.rip/"
                        value={this.state.url}
                        onChange={this.handleUrlChange}
                        className="input"
                    />
                </div>
                <div className="field">
                    <label className="label">
                        Slug
                    </label>
                    <input
                        placeholder="catchyPhrase"
                        value={this.state.slug}
                        onChange={this.handleSlugChange}
                        className="input"
                        max="100"
                    />
                </div>
                <div className="field" style={{overflow: "auto"}}>
                    <div className="is-pulled-left is-size-4" style={{wordWrap: "break-word", maxWidth: "80%"}}>
                        {this.state.error}
                    </div>
                    <button
                        className={"button is-info is-pulled-right " + (this.state.sendingRequest ? "is-loading" : "")}
                        onClick={this.submitLink}
                        disabled={!(this.state.slugValid && this.state.urlValid)}
                    >
                        Make Link
                    </button>
                </div>
            </div>
        )
    }
}

export default LinkstripForm;
