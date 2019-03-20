import React, {Component} from 'react';
import API from './Api.js';

export default class EditInvoiceForm extends Component {

    constructor(props) {
        super(props);

        this.isUnmounted = false;
        this.state = {
            submitting: false
        };
    }

    componentDidMount() {
        this.init();
    }

    componentWillUnmount() {
        this.isUnmounted = true;
    }

    updateState(state, callback) {
        if (!this.isUnmounted)
            this.setState(state, callback);
    }

    updateFieldValue(key, value) {
        let field = {};

        field[key] = value;

        this.updateState(field);
    }

    onFormSubmit(e) {
        e.preventDefault();

        const {beforeSubmit, ids, isEstimate} = this.props;
        const url = isEstimate ? 'estimates/update_multiple' : 'invoices/update_multiple';

        beforeSubmit();

        this.updateState({
            submitting: true
        });

        var formData = new FormData(e.target);

        formData.set(app.token_name, app.token);
        formData.set('ids', ids);

        API.post(url, formData, {
            headers: {'Content-Type': 'multipart/form-data'}
        })
            .then(res => res.data)
            .then(data => this.onFormSubmitted(data))
            .catch(e => this.onRequestError(e));
    }

    onFormSubmitted(data) {
        const {onSubmitted} = this.props;

        this.updateState({
            submitting: false
        });

        onSubmitted(data);
    }

    onRequestError(e) {
        console.error(e);
    }

    init() {
        flatpickr('.datepicker');
    }

    render() {
        const {isEstimate} = this.props;
        const {submitting} = this.state;

        return (
            <form method="POST" onSubmit={(e) => this.onFormSubmit(e)}>
                <div className="form-group">
                    <select name={isEstimate ? "estimate_status" : "status"} className="form-control">
                        <option value="">Select Status</option>
                        <option>Sent</option>
                        <option>Paid</option>
                        <option>Canceled</option>
                    </select>
                </div>
                <div className="input-group form-group">
                    <input type="text" name="due_date" className="datepicker form-control" placeholder="Due Date"/>
                    <span className="input-group-addon"><i className="fa fa-calendar"/></span>
                </div>
                <div className="form-submit text-right">
                    <input type="submit" className="btn btn-success" disabled={submitting}
                           value={submitting ? 'Saving...' : 'Save & Close'}/>
                </div>
            </form>
        );
    }
}