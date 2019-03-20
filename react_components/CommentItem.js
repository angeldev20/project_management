import React, {Component} from 'react';
import Avatar from "./Avatar";
import Util from "./Util";

export default class CommentItem extends Component {

    constructor(props) {
        super(props);

        this.isUnmounted = false;
        this.state = {};
    }

    componentWillReceiveProps(props) {
        this.updateState(props);
    }

    componentWillMount() {
        this.updateState(this.props);
    }

    componentWillUnmount() {
        this.isUnmounted = true;
    }

    updateState(state, callback) {
        if (!this.isUnmounted)
            this.setState(state, callback);
    }

    render() {
        const {user_info, datetime, message} = this.state;
        const {firstname, lastname, userpic} = user_info;

        return (
            <div className="comment-item">
                <div className="row comment-head">
                    <div className="col-md-2 avatar">
                        {user_info &&
                        <Avatar firstname={firstname} lastname={lastname} userpic={userpic}/>
                        }
                    </div>
                    <div className="col-md-10 name">
                        {user_info &&
                        <strong>{firstname} {lastname}</strong>
                        }
                        <span>{Util.getDateHuman(datetime, true)}</span>
                    </div>
                </div>
                <div className="message">
                    <p>{message}</p>
                </div>
            </div>
        );
    }
}