import React, {Component} from 'react';

export default class Modal extends Component {

    constructor(props) {
        super(props);

        let {id} = this.props;

        if (!id) {
            id = 'modal-' + Math.floor((Math.random() * 100) + 1);
        }

        this.isUnmounted = false;
        this.state = {
            id,
            old_dom: null
        };
    }

    componentWillReceiveProps(props) {
        this.props = props;

        this.componentDidMount();
    }

    componentDidMount() {
        const {id} = this.state;
        const {forceClose} = this.props;
        const $elem = $(`#${id}`);

        $elem.on('hidden.bs.modal', this.onRequestClose.bind(this));

        if (forceClose) {
            $elem.modal('hide');
        } else {
            $elem.modal('show');
        }
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
        const {title} = this.props;
        const {id} = this.state;

        return (
            <div className="modal fade form-popup" id={id} role="dialog">
                <div className="modal-dialog">
                    <div className="modal-content">
                        <div className="modal-header">
                            <button type="button" className="close" data-dismiss="modal"><i className="far fa-times"/>
                            </button>
                            <h4 className="modal-title">{title}</h4>
                        </div>
                        <div className="modal-body">
                            {this.props.children}
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}