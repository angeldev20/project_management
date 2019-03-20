import React, {Component} from 'react';
import API from './Api.js';
import Loader from "./Loader";

export default class MilestoneForm extends Component {

    constructor(props) {
        super(props);

        this.isUnmounted = false;
        this.state = {
            name: '',
            submitting: false,
            loading: false
        };
    }

    componentDidMount() {
        const {data} = this.props;

        if (data) {
            this.updateState({
                name: data.name
            });
        }
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

        const {data, project_id, beforeSubmit} = this.props;
        const url = (data && data.id) ? `projects/edit/${project_id}/milestones/edit` : `projects/edit/${project_id}/milestones/create`;

        beforeSubmit();

        this.updateState({
            submitting: true
        });

        var formData = new FormData(e.target);

        formData.set(app.token_name, app.token);

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

    render() {
        const {data} = this.props;
        const {loading, name, submitting} = this.state;

        return (
            <div>
                {!loading &&
                <form method="POST" onSubmit={(e) => this.onFormSubmit(e)}>
                    <div className="form-group">
                        <input value={name} onChange={(e) => this.updateFieldValue('name', e.target.value)}
                               type="text"
                               name="name"
                               className="form-control" placeholder="Name"/>
                    </div>
                    <div className="form-submit text-right">
                        {data !== undefined && data.id !== undefined &&
                        <input type="hidden" name="id" value={data.id}/>
                        }
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