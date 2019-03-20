import React, {Component} from 'react';
import API from './Api.js';
import Loader from "./Loader";
import Checkbox from "./Checkbox";

export default class ClientForm extends Component {

    constructor(props) {
        super(props);

        this.isUnmounted = false;
        this.state = {
            loading: false,
            submitting: false,
            email: null,
            firstname: null,
            lastname: null,
            company: null,
            advanced: false,
            perm_message: null,
            perm_project: null,
            perm_invoice: null,
            perm_estimates: null,
            perm_subscrition: null,
            perm_tickets: null,
        };

        this.formRef = React.createRef();
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
        this.updateState({
            loading: false
        }, () => {
            flatpickr('.datepicker');
        });
    }

    bindData() {
        this.init();
    }

    updateFieldValue(key, value) {
        let field = {};

        field[key] = value;

        this.updateState(field);

    }

    onFormSubmit(e, invite=false) {
        e.preventDefault();

        const {id, beforeSubmit} = this.props;

        beforeSubmit();

        this.updateState({
            submitting: true
        });

        var formData = new FormData(e.target);
        
        formData.append('perm_message', this.state.perm_message + ','+ this.state.perm_project + ','+ this.state.perm_invoice + ','+ this.state.perm_estimates + ','+ this.state.perm_subscrition + ','+ this.state.perm_tickets);
        // formData.append('perm_project',this.state.perm_project);
        // formData.append('perm_invoice',this.state.perm_invoice);
        // formData.append('perm_estimates',this.state.perm_estimates);
        // formData.append('perm_subscrition',this.state.perm_subscrition);
        // formData.append('perm_tickets',this.state.perm_tickets);

        formData.set(app.token_name, app.token);
        API.post('clients/create_json', formData, {
            headers: {'Content-Type': 'multipart/form-data'}
        })
            .then(res => res.data)
            .then(data => this.onFormSubmitted(data))
            .catch(e => this.onRequestError(e));
    }

    onInvite(e) {

        const {id, beforeSubmit} = this.props;

        beforeSubmit();

        this.updateState({
            submitting: true
        });

        var formData = new FormData(this.formRef.current);
        console.log(this.formRef);
        
        formData.append('perm_message', this.state.perm_message + ','+ this.state.perm_project + ','+ this.state.perm_invoice + ','+ this.state.perm_estimates + ','+ this.state.perm_subscrition + ','+ this.state.perm_tickets);
        formData.append('invite', true);
        
        formData.set(app.token_name, app.token);
        API.post('clients/create_json', formData, {
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
        const {loading, submitting, email, firstname, lastname, advanced, company, perm_message, perm_project, perm_invoice, perm_estimates, perm_subscrition, perm_tickets} = this.state;

        return (
            <div>
                {!loading &&
                <form method="POST" onSubmit={(e) => this.onFormSubmit(e, false)} ref={this.formRef}>
                    <div className="form-group">
                        <h5>First Name *</h5>
                        <input type="text" name="firstname" value={firstname ? firstname : ''} className="form-control"
                               onChange={(e) => this.updateFieldValue('firstname', e.target.value)}
                               placeholder="Enter your client's or main contact's first name"/>
                    </div>
                    <div className="form-group">
                        <h5>Last Name *</h5>
                        <input type="text" name="lastname" value={lastname ? lastname : ''} className="form-control"
                               onChange={(e) => this.updateFieldValue('lastname', e.target.value)}
                               placeholder="Enter your client's or main contact's last name"/>
                    </div>
                    <div className="form-group">
                        <h5>Email *</h5>
                        <input type="text" name="email" value={email ? email : ''} className="form-control"
                               onChange={(e) => this.updateFieldValue('email', e.target.value)}
                               placeholder="Enter your client's or main contact's email"/>
                    </div>
                    <div className="form-group">
                        <h5>Company</h5>
                        <input type="text" name="company" value={company ? company : ''} className="form-control"
                               onChange={(e) => this.updateFieldValue('company', e.target.value)}
                               placeholder="Enter a company name if they have one"/>
                    </div>
                    <div className="form-group" id="more-options" style={{display: "none"}}>
                        <div className="form-group">
                            <h5>Profile Photo</h5>
                            <div>
                                <input id="uploadFile" className="form-control" placeholder="Upload a profile photo or logo" disabled="disabled"/>
                                <div className="fileUpload inside">
                                    <span><i className="icon dripicons-upload"></i><span className="hidden-xs"></span></span>
                                    <input id="uploadBtn" type="file" name="userfile" className="upload" />
                                </div>
                            </div>
                        </div>
                        <div className="form-group">
                            <h5>Permissions</h5>
                            <ul className="accesslist">
                                <li> 
                                    <div className="">{perm_message === 'Messages' &&
                                        <i className="fa fa-check-square" onClick={(e) => this.updateFieldValue('perm_message', null)}/>
                                        }{perm_message !== 'Messages' &&
                                        <i className="far fa-square" onClick={(e) => this.updateFieldValue('perm_message', 'Messages')}/>
                                        }
                                        <div className="accessname">
                                            Message
                                        </div>
                                        
                                    </div>
                                </li>
                                <li> 
                                    <div className="">{perm_project === 'Projects' &&
                                        <i className="fa fa-check-square" onClick={(e) => this.updateFieldValue('perm_project', null)}/>
                                        }{perm_project !== 'Projects' &&
                                        <i className="far fa-square" onClick={(e) => this.updateFieldValue('perm_project', 'Projects')}/>
                                        }
                                        <div className="accessname">
                                            Projects
                                        </div>
                                        
                                    </div>
                                </li>
                                <li> 
                                    <div className="">{perm_invoice === 'Invoices' &&
                                        <i className="fa fa-check-square" onClick={(e) => this.updateFieldValue('perm_invoice', null)}/>
                                        }{perm_invoice !== 'Invoices' &&
                                        <i className="far fa-square" onClick={(e) => this.updateFieldValue('perm_invoice', 'Invoices')}/>
                                        }
                                        <div className="accessname">
                                            Invoices
                                        </div>
                                        
                                    </div>
                                </li>
                                <li> 
                                    <div className="">{perm_estimates === 'Estimates' &&
                                        <i className="fa fa-check-square" onClick={(e) => this.updateFieldValue('perm_estimates', null)}/>
                                        }{perm_estimates !== 'Estimates' &&
                                        <i className="far fa-square" onClick={(e) => this.updateFieldValue('perm_estimates', 'Estimates')}/>
                                        }
                                        <div className="accessname">
                                            Estimates
                                        </div>
                                        
                                    </div>
                                </li>
                                <li> 
                                    <div className="">{perm_tickets === 'Tickets' &&
                                        <i className="fa fa-check-square" onClick={(e) => this.updateFieldValue('perm_tickets', null)}/>
                                        }{perm_tickets !== 'Tickets' &&
                                        <i className="far fa-square" onClick={(e) => this.updateFieldValue('perm_tickets', 'Tickets')}/>
                                        }
                                        <div className="accessname">
                                            Tickets
                                        </div>
                                        
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div className="form-submit">
                        <div className="row">
                            <div className="col-md-6">
                                {!advanced &&
                                <a href="#" onClick={(e) => this.expandMoreOptions(e, true)}><i
                                    className="far fa-sliders-h"/>&nbsp;&nbsp;More options</a>
                                }
                                {advanced &&
                                <a href="#" onClick={(e) => this.expandMoreOptions(e, false)}><i
                                    className="far fa-sliders-h"/>&nbsp;&nbsp;Less options</a>
                                }
                            </div>
                            <div className="col-md-12 text-right">

                                {!submitting &&
                                    <button type="button" onClick={this.onInvite.bind(this)} className="btn btn-primary" disabled={submitting} >Send Invite
                                    </button>
                                }
                                {submitting &&
                                    <button type="button" onClick={this.onInvite.bind(this)} className="btn btn-primary" disabled={submitting} >Inviting
                                    </button>
                                    
                                }
                                &nbsp;&nbsp;&nbsp;
                                <button type="submit" className="btn btn-success" disabled={submitting}>
                                    {submitting ? 'Saving...' : 'Save'}
                                </button>
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