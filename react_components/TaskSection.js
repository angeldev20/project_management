import React, {Component} from 'react';
import API from './Api.js';
import Loader from "./Loader";
import DateLabel from "./DateLabel";
import WorkerAvatars from "./WorkerAvatars";
import Comments from "./Comments";
import CommentItem from "./CommentItem";
import CommentBox from "./CommentBox";
import Modal from "./Modal";
import AssignWorkerForm from "./AssignWorkerForm";
import Util from "./Util";

const Subtasks = ({items, onAddTask, updateStatus}) => (
    <div className="subtasks">
        <form method="POST" onSubmit={onAddTask}>
            <ul>
                {items.map(({id, name, loading, status}, index) => (
                    <li key={index}>
                        <div
                            onClick={() => {
                                if (!loading) {
                                    updateStatus(id, (status === 'done' ? 'open' : 'done'))
                                }
                            }}>{status === 'done' &&
                        <i className="fa fa-check-circle"/>}{status !== 'done' &&
                        <i className="far fa-circle"/>}</div>
                        <div>{name}</div>
                    </li>
                ))}
                <li className="new-subtask">
                    <div><i className="far fa-plus-circle"/></div>
                    <div><input type="text" name="name" placeholder="Add subtask..."/></div>
                </li>
            </ul>
        </form>
    </div>
);

export default class TaskSection extends Component {
    constructor(props) {
        super(props);

        this.isUnmounted = false;
        this.state = {
            loading: true,
            commentSubmitting: false,
            timerProcessing: false
        };
    }

    componentWillReceiveProps(props) {
        this.updateState(props, () => {
            this.getData();

            const {id, timertime, state} = this.state;
            startTimer(state, timertime, `#task-timer-${id}`);
        });
    }

    componentWillMount() {
        const {id} = this.props;

        this.updateState(this.props, () => this.getData());
    }

    componentDidMount() {
        const {id, timertime, state} = this.state;
        startTimer(state, timertime, `#task-timer-${id}`);
    }

    componentWillUnmount() {
        this.isUnmounted = true;
    }

    updateState(state, callback) {
        if (!this.isUnmounted)
            this.setState(state, callback);
    }

    getData() {
        const {id} = this.state;

        API.get(`projects/tasks/${id}/get`)
            .then(res => res.data)
            .then(data => this.initialize(data));
    }

    

    initialize(data) {
        if (data.status) {

            const old_subtasks = this.state.subtasks;
            const {id, name, due_date, workers, subtasks, comments, state, timertime} = data.data;

            if(this.state.id === id) {
                const new_subtasks = subtasks.map(t => {
                    t.loading = false;
                    old_subtasks.map(t1 => {
                        if (t1.id === t.id) {
                            t.loading = t1.loading;
                        }
                    });
                    return t;
                });

                this.updateState({
                    name,
                    due_date,
                    workers: workers.map(w => w.worker),
                    new_subtasks,
                    comments,
                    state,
                    timertime,
                    loading: false
                });
            }
        }
    }

    onAddTask(e) {
        e.preventDefault();

        let {id, subtasks} = this.state;

        let formData = new FormData(e.target);
        let subtask_id = "subtask-" + Math.floor((Math.random() * 100) + 1) + Math.floor((Math.random() * 100) + 1);

        subtasks.push({name: formData.get('name'), id: subtask_id, loading: true});
        this.updateState({subtasks});

        formData.set(app.token_name, app.token);

        API.post(`projects/tasks/${id}/subtasks/add`, formData, {
            headers: {'Content-Type': 'multipart/form-data'}
        })
            .then(res => res.data)
            .then(data => {
                subtasks = subtasks.map(t => {
                    if (t.id === subtask_id) {
                        t.id = data.subtask.id;
                        t.loading = false;
                    }
                    return t;
                });
                this.updateState({subtasks});
            });

        $(e.target).find('input[type="text"]').val("");
    }

    updateSubtaskStatus(id, status) {
        let {subtasks} = this.state;

        subtasks = subtasks.map(t => {
            if (t.id === id) {
                t.status = status;
                t.loading = true;
            }
            return t;
        });
        this.updateState({subtasks});

        let formData = new FormData();

        formData.set(app.token_name, app.token);
        formData.set('status', status);

        API.post(`projects/subtasks/${id}/edit`, formData, {
            headers: {'Content-Type': 'multipart/form-data'}
        })
            .then(res => res.data)
            .then(data => {
                subtasks = subtasks.map(t => {
                    if (t.id === id) {
                        t.loading = false;
                    }
                    return t;
                });
                this.updateState({subtasks});
            });
    }

    onPostComment(comment) {
        const {id, comments} = this.state;

        let postData = new FormData();

        postData.set(app.token_name, app.token);
        postData.set('message', comment);

        API.post(`projects/tasks/${id}/comments/add`, postData, {
            headers: {'Content-Type': 'multipart/form-data'}
        })
            .then(res => res.data)
            .then(data => {
                if (data.status) {
                    comments.push(data.comment);
                    this.updateState({comments, commentSubmitting: false});
                }
            });
    }

    onNewItemFormSubmitted(data) {
        const {onWorkersUpdate} = this.props;
        let {id, workers} = this.state;

        this.updateState({
            forceModalClose: true
        });

        workers.push(data.worker.worker);
        this.updateState({workers}, () => {
            onWorkersUpdate(id, workers);
        });
    }

    updateDate(date) {
        const due_date = Util.getDateStr(date[0]);
        const {onDateUpdate} = this.props;
        const {id} = this.state;

        var postData = new FormData();

        postData.set(app.token_name, app.token);
        postData.set('due_date', due_date);

        API.post(`projects/tasks/${id}/update`, postData, {
            headers: {'Content-Type': 'multipart/form-data'}
        })
            .then(res => {
                onDateUpdate(id, due_date);
            });
    }

    startStopTimer(e) {
        e.preventDefault();

        const {id, timerProcessing, tracking} = this.state;

        if (timerProcessing)
            return;

        this.updateState({
            timerProcessing: true,
            tracking: !(tracking > 0)
        }, () => {
            API.get(`/projects/task_start_stop_timer/${id}`)
                .then(res => res.data)
                .then(data => {
                    const {tracking, timertime, state} = data.data;

                    this.updateState({
                        timerProcessing: false,
                        tracking: tracking,
                        state: state
                    }, () => {
                        startTimer(state, timertime, `#task-timer-${id}`);
                        refreshNotification();
                    });
                });
        });
    }

    render() {
        const {loading, id, name, due_date, workers, subtasks, comments, tracking, visibleAddNewModal, forceModalClose, commentSubmitting, timertime, state, timerProcessing} = this.state;
        const {onClose} = this.props;

        return (
            <div className="shadow-box task-detail-box">
                {visibleAddNewModal &&
                <Modal title="New Assign"
                       forceClose={forceModalClose}
                       onRequestClose={() => this.updateState({visibleAddNewModal: false, forceModalClose: false})}>
                    <AssignWorkerForm
                        task_id={id}
                        beforeSubmit={() => {
                        }}
                        onSubmitted={(d) => this.onNewItemFormSubmitted(d)}/>
                </Modal>
                }
                {!loading &&
                <div>
                    <div className="table-head">
                        <div className="row">
                            <div className="col-md-10 col-sm-10">
                                <div onClick={onClose} className="close-button"><i className="far fa-times"/></div>
                                <div>{name}</div>
                            </div>
                            <div className="col-md-2 col-sm-2 text-right">
                                <i className="far fa-ellipsis-h"/>
                            </div>
                        </div>
                    </div>
                    <div className="task-options">
                        <div>
                            <div>
                                <DateLabel
                                    onChange={(selectedDates, dateStr, instance) => {
                                        this.updateDate(selectedDates);
                                    }}
                                    updateAble={true}
                                    date={due_date ? due_date : null}/>
                            </div>
                            <div>
                                <span className="label label-important">
                                    <a href="#" className={timerProcessing ? 'disabled' : ''}
                                       onClick={this.startStopTimer.bind(this)}>
                                        {(tracking === true || tracking > 0) && !timerProcessing &&
                                        <span><i className="far fa-stopwatch"/>&nbsp;<span id={`task-timer-${id}`}
                                                                                           className={state}
                                                                                           data-timerstate={state}
                                                                                           data-timertime={timertime}/>
                                        </span>
                                        }
                                        {(!tracking || tracking === false || tracking === 0) && !timerProcessing &&
                                        <span><i className="far fa-stopwatch"/>&nbsp;Start Timer</span>
                                        }
                                        {timerProcessing &&
                                        <span><i
                                            className="far fa-stopwatch"/>&nbsp;{(tracking === true || tracking > 0) ? 'Starting...' : 'Stopping...'}</span>
                                        }
                                    </a>
                                </span>
                            </div>
                            <div>
                                <span className="label label-chilled">
                                    <a href={`/projects/timesheets/${id}`} data-toggle="mainmodal">
                                    <i className="far fa-list"/>&nbsp;Timesheet
                                    </a>
                                </span>
                            </div>
                            <div>
                                <span className="label label-important">High Priority</span>
                            </div>
                        </div>
                        <div>
                            <WorkerAvatars workers={workers} allowAdd={true}
                                           onAddNewClick={(e) => {
                                               this.updateState({
                                                   visibleAddNewModal: true
                                               });
                                           }}/>
                        </div>
                    </div>
                    <Subtasks items={subtasks} updateStatus={this.updateSubtaskStatus.bind(this)}
                              onAddTask={this.onAddTask.bind(this)}/>
                    <Comments data={comments}
                              renderItem={({message, user_info, datetime}, index) => (
                                  <CommentItem key={index} user_info={user_info}
                                               datetime={datetime}
                                               message={message}/>
                              )}/>
                    <CommentBox submitting={commentSubmitting} onSubmit={this.onPostComment.bind(this)}/>
                </div>
                }
                {loading &&
                <Loader/>
                }
            </div>
        );
    }
}