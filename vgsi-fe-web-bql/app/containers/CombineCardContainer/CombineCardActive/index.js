/**
 *
 * CombineCardActive
 *
 */

import {
  Button,
  Checkbox,
  Col,
  DatePicker,
  Form,
  Input,
  Modal,
  Row,
  Select,
  Spin,
  Typography,
} from "antd";
import moment from "moment";
import PropTypes from "prop-types";
import React from "react";
import { FormattedMessage, injectIntl } from "react-intl";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import Page from "../../../components/Page/Page";
import { defaultAction, fetchApartmentAction } from "./actions";
import _ from "lodash";
import styles from "./index.less";
import messages from "../messages";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectCombineCardActive from "./selectors";
const formItemLayout = {
  labelCol: {
    xxl: { span: 10 },
    xl: { span: 9 },
  },
  wrapperCol: {
    xxl: { span: 14 },
    xl: { span: 15 },
  },
};

/* eslint-disable react/prefer-stateless-function */
@Form.create()
export class CombineCardActive extends React.PureComponent {
  constructor(props) {
    super(props);
    this.state = {
      currentSelected: -1,
    };
    this._onSearch = _.debounce(this.onSearch, 300);
  }

  componentDidMount() {
    this.props.dispatch(fetchApartmentAction());
  }

  componentDidUpdate(prevProps, prevState) {
    const { form } = this.props;
    const { validateFieldsAndScroll, getFieldValue } = form;
    if (prevState !== this.state) {
      if (
        !!getFieldValue("apartment_id") &&
        !!getFieldValue("resident_user_name")
      ) {
        validateFieldsAndScroll((errors, values) => {
          let status_apartment = values.apartment_id.split(":")[1];
          if (errors || status_apartment == 0) {
            return;
          } else {
            let apartment_id = values.apartment_id.split(":")[0];
          }
        });
      }
    }
  }

  onSearch = (keyword) => {
    this.props.dispatch(fetchApartmentAction({ name: keyword }));
  };

  handleChangeConfig = (e) => {
    this.setState({ currentSelected: e });
  };

  _onSave = () => {
    const { form, intl } = this.props;
    const { validateFieldsAndScroll } = form;
    validateFieldsAndScroll((errors, values) => {
      if (errors) {
        console.log("errors", errors);
      } else {
        let apartment_id = values.apartment_id.split(":");

        this.props.dispatch();
      }
    });
  };

  render() {
    const { cardActive, intl } = this.props;
    const { currentSelected } = this.state;
    const { apartments, create, data, loading, price } = cardActive;
    const { getFieldDecorator, getFieldValue, setFieldsValue } =
      this.props.form;
    const currentConfig = data.find((element) => element.id == currentSelected);
    const formatMessage = this.props.intl.formatMessage;

    return (
      <Page inner>
        <Row gutter={24} style={{ marginTop: 24 }}>
          <Col lg={14} xl={14} md={24} xxl={12}>
            <Form {...formItemLayout} onSubmit={this.handleSubmit}>
              <Form.Item
                label={<FormattedMessage {...messages.apartment} />}
                style={{ marginTop: 0, marginBottom: 24 }}
              >
                {getFieldDecorator("apartment_id", {
                  rules: [
                    {
                      required: true,
                      message: (
                        <FormattedMessage {...messages.emptyApartmentError} />
                      ),
                      whitespace: true,
                    },
                    {
                      validator: (rule, value, callback) => {
                        if (value) {
                          let values = value.split(":");
                          if (values.length == 2 && values[1] == 0) {
                            callback(
                              <FormattedMessage
                                {...messages.noOwnerApartment}
                              />
                            );
                          } else {
                            callback();
                          }
                        } else {
                          callback();
                        }
                      },
                    },
                  ],
                })(
                  <Select
                    style={{ width: "100%" }}
                    loading={apartments.loading}
                    showSearch
                    placeholder={
                      <FormattedMessage {...messages.choseApartment} />
                    }
                    optionFilterProp="children"
                    notFoundContent={
                      apartments.loading ? <Spin size="small" /> : null
                    }
                    onSearch={this._onSearch}
                    onChange={(value, opt) => {
                      if (!opt) {
                        this._onSearch("");
                      }
                      this.handleChangeConfig(value);
                    }}
                    allowClear
                  >
                    {apartments.lst.map((gr) => {
                      return (
                        <Select.Option
                          key={`group-${gr.id}`}
                          value={`${gr.id}:${gr.status}`}
                        >{`${gr.name} (${gr.parent_path})${
                          gr.status == 0
                            ? ` - ${(<FormattedMessage {...messages.empty} />)}`
                            : ""
                        }`}</Select.Option>
                      );
                    })}
                  </Select>
                )}
              </Form.Item>

              {this.state.currentSelected != -1 && (
                <Form.Item
                  label={formatMessage(messages.cardNumber)}
                  colon={false}
                >
                  {getFieldDecorator("number", {
                    // initialValue: currentEdit && currentEdit.number,
                    rules: [
                      {
                        required: true,
                        message: formatMessage(messages.errorEmpty, {
                          field: formatMessage(messages.cardNumber),
                        }),
                        whitespace: true,
                      },
                    ],
                  })(<Input maxLength={255} />)}
                </Form.Item>
              )}
            </Form>
          </Col>
          <Col md={24} className={styles.bookingAction}>
            <Row>
              <Col
                xxl={{
                  col: 16,
                  offset: 5,
                }}
                xl={{
                  col: 12,
                  offset: 5,
                }}
                lg={{
                  col: 24,
                  offset: 0,
                }}
                md={{
                  col: 24,
                  offset: 0,
                }}
                sm={{
                  col: 24,
                  offset: 0,
                }}
                xs={{
                  col: 24,
                  offset: 0,
                }}
              >
                <Button
                  type="danger"
                  style={{ width: 150 }}
                  onClick={(e) => {
                    Modal.confirm({
                      autoFocusButton: null,
                      title: <FormattedMessage {...messages.cancelContent} />,
                      okText: <FormattedMessage {...messages.okText} />,
                      okType: "danger",
                      cancelText: <FormattedMessage {...messages.cancelText} />,
                      onOk: () => {
                        this.props.history.push("/main/bookinglist");
                      },
                      onCancel() {},
                    });
                  }}
                >
                  <FormattedMessage {...messages.cancelText} />
                </Button>
                <Button
                  ghost
                  type="primary"
                  loading={create.loading}
                  style={{ width: 150, marginLeft: 10 }}
                  onClick={this._onSave}
                >
                  <FormattedMessage {...messages.createNew} />
                </Button>
              </Col>
            </Row>
          </Col>
        </Row>
      </Page>
    );
  }
}

CombineCardActive.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  cardActive: makeSelectCombineCardActive(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "cardActive", reducer });
const withSaga = injectSaga({ key: "cardActive", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(CombineCardActive));
