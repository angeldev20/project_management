import React, {Component} from 'react';
import {Link, Redirect} from "react-router-dom";
import API from './Api.js';
import HeaderTabs from "./HeaderTabs";
import * as Constants from "./Constants";
import EmptyContent from "./EmptyContent";
import {SortableContainer, SortableElement, SortableHandle, arrayMove} from 'react-sortable-hoc';
import DetailedRowItem from "./DetailedRowItem";
import StatusLabel from "./StatusLabel";

const Heading = ({id}) => (
    <h2 className="page-title">{id ? `Lead #${id}` : 'Build Your Form'}</h2>
);

export default class EditLead extends Component {
    constructor(props) {
        super(props);

        const {id} = props.match.params;

        this.isUnmounted = false;
        this.state = {
            redirect: false,
            loading: true,
            lead_id: id,
            name: '',
            description: '',
            payload: [],
            saving: false
        };
    }

    updateState(state, callback) {
        if (!this.isUnmounted)
            this.setState(state, callback);
    }

    componentWillMount() {
        const {lead_id} = this.state;

        this.addScripts();

        if (lead_id) {
            API.get(`leads/details/${lead_id}`)
                .then(res => res.data)
                .then(data => this.initialize(data))
                .catch(e => this.onRequestError(e));
        } else {
            this.updateState({
                loading: false
            }, () => this.setupFormBuilder());
        }
    }

    componentWillUnmount() {
        this.isUnmounted = true;
    }

    onRequestError(e) {
        console.error(e);
    }

    addScripts() {
        var s = document.createElement("script");
        s.type = "text/javascript";
        s.id = "formbuilder-vendor-js";
        s.src = "/assets/blueline/js/plugins/formbuilder-vendor.js";
        // Use any selector
        $("head").append(s);

        s = document.createElement("script");
        s.type = "text/javascript";
        s.id = "formbuilder-js";
        s.src = "/assets/blueline/js/plugins/formbuilder.js";
        // Use any selector
        $("head").append(s);
    }

    removeScripts() {
        $("script#formbuilder-js").remove();
        $("script#formbuilder-vendor-js").remove();
    }

    initialize(data) {
        if (data.status) {
            const {name, formcontent} = data.quote;
            const {fields} = JSON.parse(formcontent);

            this.updateState({
                name,
                payload: fields,
                loading: false
            }, () => this.setupFormBuilder());
        }
    }

    setupFormBuilder() {
        const {payload} = this.state;

        let fb = new Formbuilder({
            selector: '.fb-main2',
            bootstrapData: payload
        });

        var $div_right = $('<div class="col-md-9"><div class="shadow-box"/></div>');
        var $div_left = $('<div class="col-md-3"><div class="shadow-box"/></div>');

        $("#append-to-main > *").prependTo(".fb-right");
        $(".fb-right").wrap($div_right);
        $(".fb-left").wrap($div_left);

        fb.on('save', payload => {
            this.updateState({
                payload
            });
        });
    }

    updateFieldValue(e) {
        let field = {};

        field[e.target.name] = e.target.value;

        this.updateState(field);
    }

    deleteLead(e) {
        e.preventDefault();

        this.updateState({
            loading: true
        });

        const {lead_id} = this.state;

        API.delete(`leads/delete/${lead_id}`)
            .then(res => res.data)
            .then(data => this.leadDeleted(data))
            .catch(e => this.onRequestError(e));
    }

    leadDeleted(data) {
        if (data.status) {
            this.updateState({
                redirect: '/leads',
            });
        } else {
            this.updateState({
                loading: false
            });
        }
    }

    saveLead(e) {
        e.preventDefault();

        const {lead_id, name, description, payload} = this.state;

        let url = `leads/edit_or_create`;
        let postData = new FormData();

        if (lead_id) {
            url = `leads/edit_or_create/${lead_id}`;
        }

        postData.set(app.token_name, app.token);
        postData.set('name', name);
        //postData.set('description', description);
        postData.set('formcontent', payload);

        this.updateState({
            saving: true
        });

        API.post(url, postData, {
            headers: {'Content-Type': 'multipart/form-data'}
        })
            .then(res => res.data)
            .then(data => this.saved(data))
            .catch(e => this.onSaveFailed(e));
    }

    onSaveFailed(e) {
        this.updateState({
            saving: false
        });

        this.onRequestError(e);
    }

    saved(data) {
        const {lead_id} = this.state;

        if (data.status) {
            if (lead_id) {
                this.updateState({
                    saving: false
                });
            } else {
                const {id} = data.quote;

                this.updateState({
                    saving: false,
                    redirect: `/leads/edit/${id}`
                });
            }
        }
    }

    render() {
        const {
            redirect,
            loading,
            lead_id,
            name,
            description,
            saving
        } = this.state;

        if (redirect) {
            return <Redirect to={redirect} from="/leads"/>
        }

        return (
            <div>
                <HeaderTabs {...this.props} tabs={Constants.MoneyTabs}/>
                {loading &&
                <div className="tab-loader"/>
                }
                {!loading &&
                <div className="col-sm-12  col-md-12 main">
                    <div className="row">
                        <div className="tabb-header">
                            <div className="col-md-6 table-header-left">
                                <Heading id={lead_id}/>
                            </div>
                            <div className="col-md-6 text-right table-header-right">
                                {lead_id &&
                                <div><a className="transparent" target="_blank" href={`/quotation/qid/${lead_id}`}><i
                                    className="fa fa-eye"/></a></div>
                                }
                                {lead_id &&
                                <div><a className="transparent" href="#" onClick={this.deleteLead.bind(this)}><i
                                    className="far fa-trash-alt"/></a></div>
                                }
                                <div><Link to="/leads" className="btn btn-success">Back to Leads</Link></div>
                                <div><a href="#" onClick={(e) => this.saveLead(e)}
                                        className="btn btn-success">{saving ? 'Saving...' : 'Save'}</a>
                                </div>
                            </div>
                        </div>
                        <div className="clearfix"/>
                        <div className="tabb-content">
                            <div className="row">
                                <div className='fb-main2'/>
                            </div>
                            <div id="append-to-main">
                                <div className="table-head">Form Items</div>
                                <div className="new-item-form">
                                    <div className="row" style={{marginBottom: 0}}>
                                        <div className="col-md-3">
                                            <input
                                                onChange={this.updateFieldValue.bind(this)}
                                                type="text"
                                                name="name"
                                                placeholder="Name"
                                                value={name}
                                                className="form-control"/>
                                        </div>
                                        <div className="col-md-9">
                                            <input
                                                onChange={this.updateFieldValue.bind(this)}
                                                type="text"
                                                name="description"
                                                placeholder="Description"
                                                value={description}
                                                className="form-control"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                }
            </div>
        );
    }
}