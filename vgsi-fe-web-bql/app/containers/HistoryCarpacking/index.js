/**
 *
 * HistoryCarpacking
 *
 */

import _ from "lodash";
import PropTypes from "prop-types";
import React from "react";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";

import {
  Button,
  Col,
  DatePicker,
  Input,
  Row,
  Select,
  Spin,
  Table,
  Tooltip,
} from "antd";
import moment from "moment";
import queryString from "query-string";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import Page from "../../components/Page/Page";
import { defaultAction, fetchAllHistory, fetchApartment } from "./actions";
import styles from "./index.less";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectHistoryCarpacking from "./selectors";

/* eslint-disable react/prefer-stateless-function */
export class HistoryCarpacking extends React.PureComponent {
  state = {
    current: 1,
    currentEdit: undefined,
    visible: false,
    filter: {},
    downloading: false,
  };
  onSearch = (keyword) => {
    this.props.dispatch(fetchApartment({ keyword }));
  };
  _onSearch = _.debounce(this.onSearch, 300);

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
    this._unmounted = true;
  }

  componentDidMount() {
    this.reload(this.props.location.search);
    this._onSearch("");
  }

  componentWillReceiveProps(nextProps) {
    const { search } = nextProps.location;
    if (this.props.location.search != search) {
      this.reload(search);
    }
  }

  reload = (search) => {
    let params = queryString.parse(search);
    try {
      if (params.page) params.page = parseInt(params.page);
      else params.page = 1;
    } catch (error) {
      params.page = 1;
    }
    this.setState({ current: params.page, filter: params }, () => {
      this.props.dispatch(fetchAllHistory(params));
    });
  };

  handleTableChange = (pagination, filters, sorter) => {
    let sort = sorter.order == "descend" ? `-${sorter.field}` : sorter.field;
    this.setState(
      {
        sort,
        current: pagination.current,
      },
      () => {
        this.props.history.push(
          `/main/history/carpacking?${queryString.stringify({
            ...this.state.filter,
            page: this.state.current,
          })}`
        );
      }
    );
  };

  render() {
    const { auth_group, HistoryCarpacking } = this.props;
    const { loading, data, totalPage, apartment } = HistoryCarpacking;
    const { current, filter } = this.state;
    const columns = [
      {
        width: 50,
        align: "center",
        title: <span className={styles.nameTable}>#</span>,
        dataIndex: "id",
        key: "id",
        render: (text, record, index) => (
          <span>
            {Math.max(0, loading ? current - 2 : current - 1) * 20 + index + 1}
          </span>
        ),
      },
      {
        title: <span className={styles.nameTable}>Biển số</span>,
        dataIndex: "number",
        key: "number",
        // render: (text, record) => <span>{text} ({record.apartment_parent_path})</span>
      },
      {
        title: <span className={styles.nameTable}>Bất động sản</span>,
        dataIndex: "apartment_name",
        key: "apartment_name",
        render: (text, record) => {
          return (
            <>
              <span>{text}</span>
              <br />
              <span>({record.apartment_parent_path})</span>
            </>
          );
        },
      },
      {
        title: <span className={styles.nameTable}>Thời gian</span>,
        dataIndex: "datetime",
        key: "datetime",
        render: (text) => moment.unix(text).format("HH:mm DD/MM/YYYY"),
      },
      {
        title: <span className={styles.nameTable}>Trạng thái (Vào/Ra)</span>,
        dataIndex: "status",
        key: "status",
        render: (text) => {
          if (text == 1) {
            return <span className="luci-status-primary">Vào</span>;
          }
          if (text == 2) {
            return <span className="luci-status-warning">Ra</span>;
          }
        },
      },
    ];

    return (
      <Page inner className={styles.HistoryCarpackingListPage}>
        <div>
          <Row style={{ paddingBottom: 16 }} type="flex" align="middle">
            <Col span={4} style={{ marginRight: 10 }}>
              <Input.Search
                value={filter["number"] || ""}
                placeholder="Tìm kiếm số biển số"
                onChange={(e) => {
                  this.setState({
                    filter: {
                      ...filter,
                      ["number"]: e.target.value,
                    },
                  });
                }}
              />
            </Col>
            <Col span={4} style={{ marginRight: 10 }}>
              <Select
                style={{ width: "100%" }}
                loading={apartment.loading}
                showSearch
                placeholder="Chọn căn hộ"
                optionFilterProp="children"
                notFoundContent={
                  apartment.loading ? <Spin size="small" /> : null
                }
                onSearch={this._onSearch}
                onChange={(value) => {
                  this.setState(
                    {
                      filter: {
                        ...filter,
                        ["apartment_id"]: value,
                      },
                    },
                    () => {
                      // this.props.history.push(`/main/finance/fees?${queryString.stringify({
                      //   ...this.state.filter,
                      //   page: 1,
                      // })}`)
                    }
                  );
                }}
                allowClear
                value={filter["apartment_id"]}
              >
                {apartment.lst.map((gr) => {
                  return (
                    <Select.Option
                      key={`group-${gr.id}`}
                      value={`${gr.id}`}
                    >{`${gr.name} (${gr.parent_path})`}</Select.Option>
                  );
                })}
              </Select>
            </Col>
            <Col span={6} style={{ marginRight: 10 }}>
              <DatePicker.RangePicker
                key="picker"
                placeholder={["Ngày bắt đầu", "Ngày kết thúc"]}
                format="DD/MM/YYYY"
                // disabledDate={(current) => {
                //   // Can not select days before today and today
                //   return current && current > moment().endOf('day');
                // }}
                value={[
                  filter.start_datetime
                    ? moment.unix(filter.start_datetime)
                    : undefined,
                  filter.end_datetime
                    ? moment.unix(filter.end_datetime)
                    : undefined,
                ]}
                onChange={(value1, value2) => {
                  this.setState({
                    filter: {
                      ...filter,
                      ["start_datetime"]: value1[0]
                        ? value1[0].unix()
                        : undefined,
                      ["end_datetime"]: value1[1]
                        ? value1[1].unix()
                        : undefined,
                    },
                  });
                }}
              />
            </Col>
            <Button
              type="primary"
              onClick={(e) => {
                this.props.history.push(
                  `/main/history/carpacking?${queryString.stringify({
                    ...this.state.filter,
                    page: 1,
                  })}`
                );
              }}
            >
              Tìm kiếm
            </Button>
          </Row>
          <Row style={{ paddingBottom: 16 }}>
            <Tooltip title="Làm mới trang">
              <Button
                shape="circle-outline"
                style={{ padding: 0, marginRight: 10 }}
                onClick={(e) => {
                  this.reload(this.props.location.search);
                }}
                icon="reload"
                size="large"
              ></Button>
            </Tooltip>
          </Row>
          <Table
            rowKey="id"
            loading={loading}
            columns={columns}
            dataSource={data}
            locale={{ emptyText: "Không có dữ liệu" }}
            pagination={{
              pageSize: 20,
              total: totalPage,
              current,
              showTotal: (total, range) => `Tổng số ${total}`,
            }}
            onChange={this.handleTableChange}
          />
        </div>
      </Page>
    );
  }
}

HistoryCarpacking.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  HistoryCarpacking: makeSelectHistoryCarpacking(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "HistoryCarpacking", reducer });
const withSaga = injectSaga({ key: "HistoryCarpacking", saga });

export default compose(withReducer, withSaga, withConnect)(HistoryCarpacking);
