/* eslint-disable react/prop-types */
/**
 *
 * TaskEdit
 *
 */

import React from "react";
import PropTypes from "prop-types";
import { connect } from "react-redux";
import { createStructuredSelector } from "reselect";
import { compose } from "redux";

import injectSaga from "utils/injectSaga";
import injectReducer from "utils/injectReducer";
import reducer from "./reducer";
import saga from "./saga";
import Page from "../../../components/Page/Page";
import { Form, Spin } from "antd";

import { Redirect, withRouter } from "react-router";
import {
  fetchAllStaffAction,
  fetchDetailTaskAction,
  updateTask,
  defaultAction,
} from "./actions";
import makeSelectResidentAdd from "./selectors";
import { injectIntl } from "react-intl";
import TaskForm from "components/TaskForm";
import { notificationBar } from "../../../utils";
import messages from "../messages";

/* eslint-disable react/prefer-stateless-function */
@Form.create()
export class TaskEdit extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      record: {},
    };
  }

  componentDidMount() {
    const { id } = this.props.match.params;
    const { state } = this.props.location;
    if (!!id && !state) {
      this.props.dispatch(fetchDetailTaskAction({ id }));
    }
    this.props.dispatch(
      fetchAllStaffAction({
        status: "1",
      })
    );
  }

  UNSAFE_componentWillReceiveProps(nextProps) {
    if (nextProps.location.state && nextProps.location.state.record) {
      this.setState({
        record: {
          ...nextProps.location.state.record,
          people_involved: nextProps.location.state.record.people_involveds.map(
            (peo) => peo.id
          ),
          performer: nextProps.location.state.record.performers.map(
            (per) => per.id
          ),
        },
      });
    }
  }

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  onUpdate(record) {
    this.props.dispatch(updateTask({ ...record }));
  }

  render() {
    const formatMessage = this.props.intl.formatMessage;
    const {
      TaskEdit: { staff, loadingStaff, loading, task, success },
      form,
      history,
    } = this.props;
    const { id } = this.props.match.params;
    const { state } = this.props.location;

    if (success) {
      notificationBar(formatMessage(messages.updateSuccess));
      return <Redirect to="/main/task/list" />;
    }

    return (
      <Page inner>
        <Spin spinning={loading}>
          <TaskForm
            formatMessage={formatMessage}
            form={form}
            staffList={staff}
            loadingStaff={loadingStaff}
            history={history}
            record={!!id && !state ? task : this.state.record}
            isEdit={true}
            handleSubmit={(record) => this.onUpdate(record)}
            multiple={true}
          />
        </Spin>
      </Page>
    );
  }
}

TaskEdit.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  TaskEdit: makeSelectResidentAdd(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "TaskEdit", reducer });
const withSaga = injectSaga({ key: "TaskEdit", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(withRouter(injectIntl(TaskEdit)));
