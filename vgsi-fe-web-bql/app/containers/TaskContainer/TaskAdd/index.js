/* eslint-disable react/prop-types */
/**
 *
 * TaskAdd
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
import { createTask, fetchAllStaffAction, defaultAction } from "./actions";
import makeSelectResidentAdd from "./selectors";
import { injectIntl } from "react-intl";
import TaskForm from "components/TaskForm";
import { notificationBar } from "../../../utils";
import messages from "../messages";

/* eslint-disable react/prefer-stateless-function */
@Form.create()
export class TaskAdd extends React.PureComponent {
  constructor(props) {
    super(props);
    this.state = {};
  }

  componentDidMount() {
    this.props.dispatch(
      fetchAllStaffAction({
        status: "1",
      })
    );
  }

  onAdd(record) {
    this.props.dispatch(createTask({ ...record }));
  }

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  render() {
    const formatMessage = this.props.intl.formatMessage;
    const {
      taskAdd: { staff, loadingStaff, loading, success },
      form,
      history,
    } = this.props;

    if (success) {
      notificationBar(formatMessage(messages.createSuccess));
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
            handleSubmit={(record) => this.onAdd(record)}
          />
        </Spin>
      </Page>
    );
  }
}

TaskAdd.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  taskAdd: makeSelectResidentAdd(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "TaskAdd", reducer });
const withSaga = injectSaga({ key: "TaskAdd", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(withRouter(injectIntl(TaskAdd)));
