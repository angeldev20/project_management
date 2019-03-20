import React, {Component} from 'react';
import WorkerAvatars from "./WorkerAvatars";
import DateLabel from "./DateLabel";
import Util from "./Util";
import Modal from "./Modal";
import AssignWorkerForm from "./AssignWorkerForm";

export default class NewTaskRow extends Component {

    constructor(props) {
        super(props);

        this.isUnmounted = false;
        this.state = {
            visibleAddNewModal: false,
            forceModalClose: false,
            name: '',
            date_value: '',
            workers: []
        };
    }

    componentWillMount() {
        this.updateState(this.props);
    }

    componentWillUnmount() {
        this.isUnmounted = true;
    }

    updateState(state, callback) {
        if (!this.isUnmounted)
            this.setState(state, callback);
    }

    onEditing(e) {
        const {onEditing} = this.props;

        this.updateState({
            name: e.target.value
        });

        onEditing(e);
    }

    onAddNewClick(e) {
        e.preventDefault();

        this.updateState({
            visibleAddNewModal: true
        });
    }

    onNewItemFormSubmitted(data) {
        let {workers} = this.state;

        if (data && data[0] !== undefined)
            workers.push(data[0]);

        this.updateState({
            forceModalClose: true,
            workers
        });
    }

    onDateChange(selectedDates, dateStr, instance) {
        const date_value = Util.getDateStr(selectedDates[0]);

        this.updateState({
            date_value
        });
    }

    onSubmit(e) {
        e.preventDefault();

        const {task_id, onSave} = this.props;
        const {date_value, workers, name} = this.state;

        onSave(task_id, name, date_value, workers.map(w => w.id));
    }

    reset() {
        this.updateState({
            name: '',
            date_value: '',
            workers: []
        });
    }

    render() {
        const {task_id} = this.props;
        const {date_value, name, workers, visibleAddNewModal, forceModalClose} = this.state;

        return (
            <div>
                {visibleAddNewModal &&
                <Modal title="New Assign"
                       forceClose={forceModalClose}
                       onRequestClose={() => this.updateState({visibleAddNewModal: false, forceModalClose: false})}>
                    <AssignWorkerForm
                        beforeSubmit={() => {
                        }}
                        onSubmitted={(d) => this.onNewItemFormSubmitted(d)}/>
                </Modal>
                }
                <form onSubmit={(e) => this.onSubmit(e)}>
                    <div className="single-task new-task">
                        <div className="col-md-4">
                            <div className="handle-container" onClick={this.reset.bind(this)}>
                                <i className="far fa-times"/>
                            </div>
                            <div className="task-check">
                                <i className="far fa-plus-circle"/>
                            </div>
                            <div style={{width: "70%"}}><input id={`new-task-${task_id}`}
                                                               onChange={this.onEditing.bind(this)}
                                                               value={name}
                                                               type="text"
                                                               placeholder="Add a new item…"/></div>
                        </div>
                        <div className="col-md-4">
                            <DateLabel
                                onChange={this.onDateChange.bind(this)}
                                date={date_value}
                                updateAble={true}/>
                        </div>
                        <div className="col-md-4 text-right actions">
                            <div>
                                <WorkerAvatars workers={workers}
                                               allowAdd={true}
                                               onAddNewClick={this.onAddNewClick.bind(this)}/>
                            </div>
                            <div>
                                <button onClick={(e) => this.onSubmit(e)} className="transparent"><i
                                    className="far fa-check"/>
                                </button>
                            </div>
                        </div>
                        <div className="clearfix"/>
                    </div>
                </form>
            </div>
        );
    }
}