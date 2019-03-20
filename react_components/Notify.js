import React, {Component} from 'react';

export default class Notify extends Component {

    constructor(props) {
        super(props);

        let {id} = this.props;

        if (!id) {
            id = 'notify-' + Math.floor((Math.random() * 100) + 1);
        }

        this.isUnmounted = false;
        this.state = {
            id
        };
    }

    componentWillReceiveProps(props) {
        this.props = props;

        this.componentDidMount();
    }

    componentDidMount() {
        const {id} = this.state;
        const $elem = $(`#${id}`);

        $elem.velocity({
            opacity: 1,
            right: "10px",
        }, 800, () => {
            $elem.delay(3000).fadeOut(() => {
                this.onRequestClose();
            });
        });
    }

    componentWillUnmount() {
        this.isUnmounted = true;
    }

    updateState(state, callback) {
        if (!this.isUnmounted)
            this.setState(state, callback);
    }

    onRequestClose() {
        const {onRequestClose} = this.props;

        onRequestClose();
    }

    render() {
        const {type, message} = this.props;
        const {id} = this.state;

        return (
            <div className={`notify ${type}`} id={id}>{message}</div>
        );
    }
}