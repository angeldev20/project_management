import React, {Component} from 'react';
import API from './Api.js';
import Loader from "./Loader";

export default class AssignWorkerForm extends Component {

    constructor(props) {
        super(props);

        this.isUnmounted = false;
        this.state = {
            loading: true,
            users: [],
            users_loaded: false,
            submitting: false,
            user_id: null
        };
    }

    componentDidMount() {
        this.bindData();
    }

    componentWillUnmount() {
        this.isUnmounted = true;
    }

    updateState(state, callback) {
        if (!this.isUnmounted)
            this.setState(state, callback);
    }

    bindData() {
        API.get(`team/data`)
            .then(res => res.data)
            .then(data => {
                this.updateState({
                    users: data.users,
                    users_loaded: true,
                    loading: false
                });
            });
    }

    updateFieldValue(key, value) {
        let field = {};

        field[key] = value;

        this.updateState(field);
    }

    onFormSubmit(e) {
        e.preventDefault();

        const {users} = this.state;
        const {task_id, beforeSubmit} = this.props;

        beforeSubmit();

        this.updateState({
            submitting: true
        });

        var formData = new FormData(e.target);
        const user_id = formData.get('user_id');

        formData.set(app.token_name, app.token);

        if (task_id) {
            API.post(`projects/tasks/${task_id}/workers/add`, formData, {
                headers: {'Content-Type': 'multipart/form-data'}
            })
                .then(res => res.data)
                .then(data => this.onFormSubmitted(data));
        } else {
            let selected_users = [];

            users.map(u => {
                if ((u.id + "") === (user_id + "")) {
                    selected_users.push(u);
                }
            });

            this.onFormSubmitted(selected_users);
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
        const {loading, users_loaded, users, submitting, user_id} = this.state;

        return (
            <div>
                {!loading &&
                <form method="POST" onSubmit={(e) => this.onFormSubmit(e)}>
                    <div className="form-group">
                        <select name="user_id" className="form-control" disabled={!users_loaded}
                                onChange={(e) => this.updateFieldValue('user_id', e.target.value)}
                                value={user_id ? user_id : ''}>
                            <option value="">{loading ? 'Loading...' : 'Select User'}</option>
                            {users.map((user, index) => (
                                <option key={index} value={user.id}>{`${user.firstname} ${user.lastname}`}</option>
                            ))}
                        </select>
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