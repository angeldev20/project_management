import React, {Component} from 'react';
import * as Constants from "./Constants";
import {Redirect} from "react-router-dom";
import API from './Api.js';
import {arrayMove, SortableContainer, SortableElement, SortableHandle} from "react-sortable-hoc";
import Grid from "./Grid";
import EmptyContent from "./EmptyContent";
import Notify from "./Notify";
import ClientGridItem from "./ClientGridItem";
import Checkbox from "./Checkbox";


export default class TimeTrackView extends Component {

    constructor(props) {

        super(props);

        this.isUnmounted = false;
        this.state = {
            invoice: props.invoice_id,
            loading: true,
            timetracks: [],
        };

        this.formRef = React.createRef();
    }

    componentWillMount() {
        this.getData();
    }

    componentWillUnmount() {
        this.isUnmounted = true;
    }

    updateState(state, callback) {
        if (!this.isUnmounted)
            this.setState(state, callback);
    }

    updateInvoice(index_track,index_item,invoice){
        // console.log(index_track + ' '+invoice_id);
        this.state.timetracks[index_track].timesheet[index_item].invoice_id = invoice;
        this.updateState({
            timetracks: this.state.timetracks
        });
    }
    getData() {
        this.updateState({
            loading: true
        });

        API.get(`tinvoices/timesheets/${this.state.invoice}`)
            .then(res => res.data)
            .then(data => this.initialize(data));
    }

    initialize(data) {
        
        if (data.status) {
            this.updateState({
                timetracks: data.data,
                loading: false
            });
        }
        console.log(this.state.timetracks);
    }

    onRequestError(e) {
        console.error(e);
    }

    onAddNewClick(e) {
        e.preventDefault();

        //$("#add-new-button").trigger("click");

        
    }

    onFormSubmit(e, invite=false) {
        e.preventDefault();

        const {id, beforeSubmit} = this.props;

        beforeSubmit();

        this.updateState({
            submitting: true
        });

        var formData = new FormData;
        
        var id_list = "";
        var invoice_list = "";

        this.state.timetracks.map((timetrack, index_item) => {
            timetrack.timesheet.map((item, index) => {
                id_list += $item.id + ",";
                invoice_list += $item.invoice_id + ",";
            });
        });
        formData.set('ids', id_list);
        formData.set('invoics', invoice_list);
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

    renderTimetrack(timetrack, index_track) {
        return (
            <div>
                <h4>{timetrack.date}</h4>
                {timetrack.timesheet.map((item, index_item) => (
                    <div>
                        <div className="handle-container clickable"></div>
                        <div className="task-check clickable" style={{float:"left",marginRight:"10px"}}>
                            {item.invoice_id !== 0 &&
                                <i className="fa fa-check-square" onClick={() => this.updateInvoice(index_track, index_item, 0)}/>
                            }
                            {item.invoice_id === 0 &&
                                <i className="far fa-square" onClick={() => this.updateInvoice(index_track, index_item, this.state.invoice_id)}/>
                            }
                        </div>
                        <div className="task-name">{item.tracked_hours} Hours tracked on {item.project_name}</div>
                    </div>
                ))}
            </div>
        );
    }

   

    render() {
        const {
            invoice,
            loading,
            timetracks
        } = this.state;
        const {submitting} = this.state;

        return (
            <div>
                {loading &&
                <div className="tab-loader"/>
                }
                {!loading &&
                <form method="POST" onSubmit={(e) => this.onFormSubmit(e, false)} ref={this.formRef}>
                    <div>
                        <div className="row">
                            {timetracks.length > 0 &&
                            <div>
                                <div className="clearfix" />
                                <div className="tabb-content">
                                    {timetracks.map((timetrack, index) => (this.renderTimetrack(timetrack, index)))}
                                </div>
                            </div>
                            }
                            {timetracks.length <= 0 &&
                            <EmptyContent
                                title="You haven’t tracked  any times, yet!"
                                />
                            }
                            <div className="col-md-12 col-sm-12 form-submit text-right">
                                <input type="submit" className="btn btn-success" disabled={submitting}
                                    value={submitting ? 'Saving...' : 'Save & Close'}/>
                            </div>
                        </div>
                    </div>
                </form>
                }
            </div>
        );
    }
}