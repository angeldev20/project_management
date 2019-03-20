import React, {Component} from 'react';
import API from './Api.js';
import Loader from "./Loader";

export default class ExpenseForm extends Component {

    constructor(props) {
        super(props);

        this.isUnmounted = false;
        this.state = {
            loading: true,
            projects: [],
            projects_loaded: false,
            submitting: false,
            description: null,
            type: null,
            category: null,
            date: null,
            project_id: null,
            rebill: null,
            recurring: null,
            status: null,
            value: null,
            currency: null,
            attachment_description: null
        };
    }

    componentDidMount() {
        this.bindData();
        this.init();
    }

    componentWillUnmount() {
        this.isUnmounted = true;
    }

    updateState(state, callback) {
        if (!this.isUnmounted)
            this.setState(state, callback);
    }

    init() {
        flatpickr('.datepicker');
    }

    bindData() {
        const {id} = this.props;

        API.get(`projects/data`)
            .then(res => res.data)
            .then(data => {
                this.updateState({
                    projects: data.projects,
                    projects_loaded: true
                });
            });

        if (id) {
            API.get(`/expenses/get/${id}`)
                .then(res => res.data)
                .then(data => {
                    const {description, type, category, date, project_id, rebill, recurring, status, value, currency, attachment_description} = data.expense;
                    this.updateState({
                        description,
                        type,
                        category,
                        date,
                        project_id,
                        rebill,
                        recurring,
                        status,
                        value,
                        currency,
                        attachment_description,
                        loading: false
                    }, () => this.init());
                });
        } else {
            this.updateState({
                loading: false
            }, () => this.init());
        }
    }

    updateFieldValue(key, value) {
        let field = {};

        field[key] = value;

        this.updateState(field);
    }

    onFormSubmit(e) {
        e.preventDefault();

        const {id, beforeSubmit} = this.props;

        beforeSubmit();

        this.updateState({
            submitting: true
        });

        var formData = new FormData(e.target);

        formData.set(app.token_name, app.token);

        if (id) {
            API.post(`expenses/edit/${id}`, formData, {
                headers: {'Content-Type': 'multipart/form-data'}
            })
                .then(res => res.data)
                .then(data => this.onFormSubmitted(data))
                .catch(e => this.onRequestError(e));
        } else {
            API.post('expenses/create', formData, {
                headers: {'Content-Type': 'multipart/form-data'}
            })
                .then(res => res.data)
                .then(data => this.onFormSubmitted(data))
                .catch(e => this.onRequestError(e));
        }
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

    render() {
        const {loading, projects_loaded, projects, submitting, description, type, category, date, project_id, rebill, recurring, status, value, currency, attachment_description} = this.state;

        return (
            <div>
                {!loading &&
                <form method="POST" onSubmit={(e) => this.onFormSubmit(e)}>
                    <div className="form-group">
                        <select name="type" className="form-control" value={type ? type : ''}
                                onChange={(e) => this.updateFieldValue('type', e.target.value)}>
                            <option value="">Payment Type</option>
                            <option>payment</option>
                            <option>recurring_payment</option>
                            <option>refund</option>
                        </select>
                    </div>
                    <div className="form-group">
                        <select name="category" className="form-control" value={category ? category : ''}
                                onChange={(e) => this.updateFieldValue('category', e.target.value)}>
                            <option value="">Select Category</option>
                            <option>Accommodation</option>
                            <option>Accountancy Fees</option>
                            <option>Advertising and Promotion</option>
                            <option>Auto Expenses</option>
                            <option>Cell Phone</option>
                            <option>Computer Hardware</option>
                            <option>Computer Software</option>
                            <option>Insurance</option>
                            <option>Leasing Payments</option>
                            <option>Office Costs</option>
                            <option>Postage</option>
                            <option>Staff Training</option>
                            <option>Subscriptions</option>
                            <option>Web Hosting</option>
                            <option>Travel</option>
                            <option>Materials</option>
                            <option>Rent</option>
                        </select>
                    </div>
                    <div className="form-group">
                        <input type="text" name="description" value={description} className="form-control"
                               onChange={(e) => this.updateFieldValue('description', e.target.value)}
                               placeholder="Description"/>
                    </div>
                    <div className="form-group">
                        <select name="status" className="form-control" value={status}
                                onChange={(e) => this.updateFieldValue('status', e.target.value)}>
                            <option value="">Select Status</option>
                            <option>Open</option>
                            <option>Paid</option>
                            <option>Canceled</option>
                        </select>
                    </div>
                    <div className="input-group form-group">
                        <input type="text" name="date" value={date} className="datepicker form-control"
                               placeholder="Date"
                               onChange={(e) => this.updateFieldValue('date', e.target.value)}/>
                        <span className="input-group-addon"><i className="far fa-calendar-alt"/></span>
                    </div>
                    <div className="form-group">
                        <select name="recurring" className="form-control" value={recurring ? recurring : ''}
                                onChange={(e) => this.updateFieldValue('recurring', e.target.value)}>
                            <option value="">Recurring Payment</option>
                            <option>+7 day</option>
                            <option>+14 day</option>
                            <option>+1 month</option>
                            <option>+3 month</option>
                            <option>+6 month</option>
                            <option>+1 year</option>
                        </select>
                    </div>
                    <div className="form-group">
                        <input type="number" name="value" value={value} className="decimal form-control"
                               onChange={(e) => this.updateFieldValue('value', e.target.value)}
                               placeholder="Amount"/>
                    </div>
                    <div className="form-group">
                        <input type="text" name="currency" value={currency} className="form-control"
                               onChange={(e) => this.updateFieldValue('currency', e.target.value)}
                               placeholder="Currency"/>
                    </div>
                    <div className="form-group">
                        <select name="project_id" className="form-control" disabled={!projects_loaded}
                                onChange={(e) => this.updateFieldValue('project_id', e.target.value)}
                                value={project_id ? project_id : ''}>
                            <option value="">{loading ? 'Loading...' : 'Select Project'}</option>
                            {projects.map((project, index) => (
                                <option key={index} value={project.id}>{project.name}</option>
                            ))}
                        </select>
                    </div>
                    <div className="form-group">
                        <select name="rebill" className="form-control" value={rebill ? rebill : ''}
                                onChange={(e) => this.updateFieldValue('rebill', e.target.value)}>
                            <option value="">Rebill</option>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div className="fileUpload input-group form-group">
                        <input id="uploadFile" className="form-control uploadFile" placeholder="Attachment"
                               disabled="disabled"/>
                        <input id="uploadBtn" type="file" name="userfile" className="upload"/>
                        <span className="input-group-addon"><i className="fa fa-upload"/></span>
                    </div>
                    <div className="form-group hide">
                        <input type="text" name="attachment_description" value={attachment_description}
                               onChange={(e) => this.updateFieldValue('attachment_description', e.target.value)}
                               className="form-control"
                               placeholder="Attachment Description"/>
                    </div>
                    <div className="form-submit text-right">
                        <input type="submit" className="btn btn-success" disabled={submitting}
                               value={submitting ? 'Saving...' : 'Save & Close'}/>
                    </div>
                </form>
                }
                {loading &&
                <Loader />
                }
            </div>
        );
    }
}