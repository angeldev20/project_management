import React, {Component} from 'react';
import API from './Api.js';

export default class DeleteModal extends Component {

    constructor(props) {
        super(props);

        let {id} = this.props;

        if (!id) {
            id = 'delete-modal-' + Math.floor((Math.random() * 100) + 1) + Math.floor((Math.random() * 100) + 1);
        }

        this.isUnmounted = false;
        this.state = {
            id
        };
    }

    componentWillReceiveProps(props) {
        this.props = props;
    }

    componentDidMount() {
        const {trigger} = this.props;
        const {id} = this.state;

        var $elem = $(`#${id}`);

        $(document).on("click", trigger, function (e) {
            e.preventDefault();

            const $btn = $elem.find('.btn-confirm');

            $btn.removeAttr("disabled");
            $btn.text("Confirm");
            $btn.attr('data-href', $(this).data('href'));

            $elem.modal("show");
        });
    }

    componentWillUnmount() {
        this.isUnmounted = true;

        const {trigger} = this.props;

        $(document).off("click", trigger);
    }

    updateState(state, callback) {
        if (!this.isUnmounted)
            this.setState(state, callback);
    }

    onRequestClose() {
        const {onRequestClose} = this.props;

        onRequestClose();
    }

    onCancel(e) {
        e.preventDefault();

        const {id} = this.state;
        const $elem = $(`#${id}`);

        $elem.modal('hide');
    }

    onConfirm(e) {
        e.preventDefault();

        const {onItemDelete} = this.props;
        const {id} = this.state;
        const $elem = $(`#${id}`);
        const $btn = $elem.find('.btn-confirm');

        $btn.attr('disabled', 'disabled');
        $btn.text('Deleting...');

        API.get($btn.attr('data-href'))
            .then(res => res.data)
            .then(data => {
                $elem.modal('hide');

                onItemDelete(data);
            });
    }

    render() {
        const {title, text} = this.props;
        const {id} = this.state;

        return (
            <div className="modal fade form-popup" id={id} role="dialog">
                <div className="modal-dialog">
                    <div className="modal-content">
                        <div className="modal-header">
                            <button type="button" className="close" data-dismiss="modal"><i className="fa fa-times"/>
                            </button>
                            <h4 className="modal-title">{title}</h4>
                        </div>
                        <div className="modal-body">
                            <p>{text}</p>
                            <div className="form-submit text-right">
                                <a href="#" onClick={(e) => this.onCancel(e)}
                                   className="btn btn-default">Cancel</a>&nbsp;
                                <a href="#" className="btn btn-danger btn-confirm" onClick={(e) => this.onConfirm(e)}>Confirm</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}