import React, {Component} from 'react';
import API from './Api.js';
import Loader from "./Loader";
import Checkbox from "./Checkbox";

export default class TeamMemberForm extends Component {

    constructor(props) {
        super(props);

        this.isUnmounted = false;
        this.state = {
            loading: false,
            projects: [],
            projects_loaded: false,
            submitting: false,
            email: null,
            queue: null,
            status: null,
            admin: null,
            super_admin: 0,
            permissions: [],
            advanced: false,
            queues: [],
            queues_loaded: false,
            firstname: null,
            company: null
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
        const {queues_loaded} = this.state;

        if (queues_loaded) {
            this.updateState({
                loading: false
            }, () => {
                flatpickr('.datepicker');
            });
        }
    }

    bindData() {
        API.get(`team/queues`)
            .then(res => res.data)
            .then(data => {
                this.updateState({
                    queues: data.queues,
                    queues_loaded: true
                }, this.init());
            });
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

        API.post('team/invite', formData, {
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

    expandMoreOptions(e, expand) {
        e.preventDefault();

        this.updateState({
            advanced: expand
        }, () => {
            $("#more-options").slideToggle();
        });
    }

    render() {
        const {loading, submitting, email, advanced, queues, queues_loaded, status, queue, firstname, company, admin} = this.state;

        return (
            <div>
                {!loading &&
                <form method="POST" onSubmit={(e) => this.onFormSubmit(e)}>
                    <div className="form-group">
                        <input type="text" name="firstname" value={firstname ? firstname : ''} className="form-control"
                               onChange={(e) => this.updateFieldValue('firstname', e.target.value)}
                               placeholder="Name"/>
                    </div>
                    <div className="form-group">
                        <input type="text" name="email" value={email ? email : ''} className="form-control"
                               onChange={(e) => this.updateFieldValue('email', e.target.value)}
                               placeholder="Email"/>
                    </div>
                    <div className="form-group">
                        <input type="hidden" name="company" value={company ? company : ''} className="form-control"
                               onChange={(e) => this.updateFieldValue('company', e.target.value)}
                               placeholder="Company Name"/>
                    </div>
                    <div id="more-options" style={{display: "none"}}>
                        <div className="form-group">
                            <select name="queue" className="form-control" disabled={!queues_loaded}
                                    onChange={(e) => this.updateFieldValue('queue', e.target.value)}
                                    value={queue ? queue : ''}>
                                <option value="">{loading ? 'Loading...' : 'Select Queue'}</option>
                                {queues.map((queue, index) => (
                                    <option key={index} value={queue.id}>{queue.name}</option>
                                ))}
                            </select>
                        </div>
                        <div className="form-group">
                            <select name="status" className="form-control" value={status ? status : ''}
                                    onChange={(e) => this.updateFieldValue('status', e.target.value)}>
                                <option value="">Select Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div className="form-group">
                            <select name="admin" className="form-control" value={admin ? admin : ''}
                                    onChange={(e) => this.updateFieldValue('admin', e.target.value)}>
                                <option value="">Super Admin</option>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>
                    <div className="form-submit">
                        <div className="row">
                            <div className="col-md-6">
                                {!advanced &&
                                <a href="#" onClick={(e) => this.expandMoreOptions(e, true)}><i
                                    className="fa fa-sliders"/>&nbsp;More options</a>
                                }
                                {advanced &&
                                <a href="#" onClick={(e) => this.expandMoreOptions(e, false)}><i
                                    className="fa fa-sliders"/>&nbsp;Less options</a>
                                }
                            </div>
                            <div className="col-md-6 text-right">
                                <input type="submit" className="btn btn-success" disabled={submitting}
                                       value={submitting ? 'Saving...' : 'Save & Close'}/>
                            </div>
                        </div>
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