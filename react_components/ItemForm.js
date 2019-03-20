import React, {Component} from 'react';
import API from './Api.js';
import Loader from "./Loader";

export default class ItemForm extends Component {

    constructor(props) {
        super(props);

        this.isUnmounted = false;
        this.state = {
            type: '',
            name: '',
            value: '',
            description: '',
            submitting: false,
            loading: true
        };
    }

    componentDidMount() {
        const {id} = this.props;

        if (id) {
            API.get(`/items/get/${id}`)
                .then(res => res.data)
                .then(data => {
                    const {name, type, value, description} = data.item;
                    this.updateState({
                        description,
                        type,
                        name,
                        value,
                        loading: false
                    });
                });
        } else {
            this.updateState({
                loading: false
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

        const {id, beforeSubmit} = this.props;

        beforeSubmit();

        this.updateState({
            submitting: true
        });

        var formData = new FormData(e.target);

        formData.set(app.token_name, app.token);

        if (id) {
            API.post(`items/edit/${id}`, formData, {
                headers: {'Content-Type': 'multipart/form-data'}
            })
                .then(res => res.data)
                .then(data => this.onFormSubmitted(data))
                .catch(e => this.onRequestError(e));
        } else {
            API.post('items/create_item', formData, {
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
        const {loading, type, name, value, description, submitting} = this.state;

        return (
            <div>
                {!loading &&
                <form method="POST" onSubmit={(e) => this.onFormSubmit(e)}>
                    <div className="form-group">
                        <label for="type">Type </label>
                        <input value={type} onChange={(e) => this.updateFieldValue('type', e.target.value)}
                               type="text"
                               name="type"
                               className="form-control" />
                    </div>
                    <div className="form-group">
                        <label for="name">Name </label>
                        <input value={name} onChange={(e) => this.updateFieldValue('name', e.target.value)}
                               type="text"
                               name="name"
                               className="form-control" />
                    </div>
                    <div className="form-group">
                        <label for="value">Value </label>
                        <input value={value} onChange={(e) => this.updateFieldValue('value', e.target.value)}
                               type="number"
                               name="value"
                               className="form-control" step="any"/>
                    </div>
                    <div className="form-group">
                        <label for="description">Description </label>
                        <textarea value={description}
                                  onChange={(e) => this.updateFieldValue('description', e.target.value)}
                                  name="description"
                                  className="form-control" />
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