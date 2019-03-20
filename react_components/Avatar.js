import React, {Component} from 'react';
import API from './Api.js';

export default class Avatar extends Component {
    constructor(props) {
        super(props);

        this.id = 'avatar-' + Math.floor((Math.random() * 100) + 1);
        this.isUnmounted = false;
        this.state = {
            firstname: null,
            lastname: null,
            userpic: null,
            loaded: false
        };
    }

    componentDidMount() {
        const {id, userpic, firstname, lastname, placeholder} = this.props;

        if (!placeholder) {
            if (!firstname && !lastname && !userpic && id) {
                API.get(`users/get/${id}`)
                    .then(res => res.data)
                    .then(data => {
                        if (data.status) {
                            const {firstname, lastname, userpic} = data.user;

                            this.updateState({
                                firstname,
                                lastname,
                                userpic,
                                loaded: true
                            }, () => this.init());
                        }
                    });
            } else {
                this.updateState({
                    firstname,
                    lastname,
                    userpic,
                    loaded: true
                }, () => this.init());
            }
        } else {
            this.updateState({
                loaded: true
            });
        }
    }

    componentWillUnmount() {
        this.isUnmounted = true;
    }

    updateState(state, callback) {
        if (!this.isUnmounted)
            this.setState(state, callback);
    }

    init() {
        var $elem = $(`#${this.id}`);
        $elem.fadeIn();
    }

    render() {
        let {firstname, lastname, userpic, loaded, placeholder} = this.state;

        if (loaded) {
            let initials = ' ';

            if (!placeholder && firstname && lastname)
                initials = firstname.substring(0, 1) + lastname.substring(0, 1);
            else {
                firstname = '';
                lastname = '';
            }

            if (!placeholder && userpic && userpic !== 'no-pic.png') {
                return <img id={this.id} src={userpic} className="avatar-image"/>
            } else {
                return <a id={this.id} className="initials-avatar-circle rainbow tt" title=""
                          data-original-title={`${firstname} ${lastname}`}>{initials}</a>;
            }
        }
        return <span/>;
    }
}