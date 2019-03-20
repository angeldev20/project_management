import React, {Component} from 'react';
import Avatar from "./Avatar";

export default class CommentBox extends Component {

    constructor(props) {
        super(props);

        this.id = "comment-box-" + Math.floor((Math.random() * 100) + 1) + Math.floor((Math.random() * 100) + 1);
        this.isUnmounted = false;
        this.state = {
            comment: '',
            submitting: false
        };
    }

    componentWillReceiveProps(props) {
        this.updateState(props);
    }

    componentWillMount() {
        this.updateState(this.props);

        $(document).on("keypress", `#${this.id}`, (e) => {
            if (e.which === 13) {
                this.onSubmit();

                return false;
            }
        });
    }

    componentWillUnmount() {
        this.isUnmounted = true;
    }

    updateState(state, callback) {
        if (!this.isUnmounted)
            this.setState(state, callback);
    }

    onSubmit() {
        const {onSubmit} = this.props;
        const {comment} = this.state;

        this.updateState({
            comment: '',
            submitting: true
        });

        onSubmit(comment);
    }

    updateFieldValue(key, value) {
        let field = {};

        field[key] = value;

        this.updateState(field);
    }

    render() {
        const {comment, submitting} = this.state;

        return (
            <div className="comment-box">
                <form method="POST" onSubmit={this.onSubmit.bind(this)}>
                    <textarea disabled={submitting} value={comment}
                              onChange={(e) => this.updateFieldValue('comment', e.target.value)}
                              id={this.id} name="comment" placeholder="Write a comment..."/>
                </form>
            </div>
        );
    }
}